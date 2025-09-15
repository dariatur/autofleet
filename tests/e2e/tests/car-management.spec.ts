import { expect } from "@playwright/test";

import { test } from '../fixture';
import { CarFactory } from '../entity/car-factory';


const factory = new CarFactory();
const cars = factory.createMany(1);
const car = factory.create();

const carForUpdate = { make: 'Toyota', model: 'Camry', year: 2000, price: 20000 };
const updatedCar = {...carForUpdate, price: car.price + 5000 };

// const cars = [
//     { make: 'Toyota', model: 'Camry', year: 2000, price: 20000 },
//     { make: 'Honda', model: 'Accord', year: 2010, price: 15000 },
//     { make: 'Ford', model: 'Mustang', year: 2015, price: 25000 },
// ];

test.describe('Car Management Page', () => {

    test.beforeEach(async ({ pages, carsApi }) => {
        await pages.carManagementPage.navigateTo();
        await carsApi.deleteCars();
    });

    cars.forEach((car) => {
        test(`add new car: ${car.make} ${car.model} ${car.year} ${car.price}`, async ({ pages }) => {
            test.setTimeout(10000);

        const responsePromise = pages.page.waitForResponse(resp =>
            resp.url().includes('/api/cars') && resp.request().method() === 'POST'
        );

        await pages.carManagementPage.clickOnAddNewCarButton();
        await pages.carManagementPage.newCarModalPage.addCar(car);

        const response = await responsePromise;
        expect(response.status()).toBe(201);

        const data = await response.json();
        expect(data).toEqual(expect.objectContaining(car));

        await expect(pages.carManagementPage.alertMessage)
            .toContainText(pages.carManagementPage.CAR_MESSAGES.ADDED_SUCCESS);
        await expect(pages.carManagementPage.getCarRow(car)).not.toBeNull();
        })
    });

    test(`update car: ${carForUpdate.make} ${carForUpdate.model} ${carForUpdate.year} ${carForUpdate.price}`, async ({ pages }) => {

        const responsePromise = pages.page.waitForResponse(resp =>
            resp.url().includes('/api/cars') && resp.request().method() === 'PATCH'
        );

        await pages.carManagementPage.clickOnAddNewCarButton();
        await pages.carManagementPage.newCarModalPage.addCar(carForUpdate);

        await pages.carManagementPage.clickOnEditButton(carForUpdate);
        await pages.carManagementPage.editCarModalPage.updateCar(updatedCar);

        const response = await responsePromise;
        expect(response.status()).toBe(200);
        expect(await response.json()).toEqual(expect.objectContaining(updatedCar));

        await expect(pages.carManagementPage.alertMessage)
            .toContainText(pages.carManagementPage.CAR_MESSAGES.UPDATED_SUCCESS);

        await expect(pages.carManagementPage.getCarRow(updatedCar)).not.toBeNull();
    });


    test(`delete car: ${car.make} ${car.model} ${car.year} ${car.price}`, async ({ pages }) => {
        const responsePromise = pages.page.waitForResponse(resp =>
            resp.url().includes('/api/cars') && resp.request().method() === 'DELETE'
        );

        await pages.carManagementPage.clickOnAddNewCarButton();
        await pages.carManagementPage.newCarModalPage.addCar(car);

        await pages.carManagementPage.deleteCar(car);

        const response = await responsePromise;
        expect(response.status()).toBe(204);
        
        await expect(pages.carManagementPage.alertMessage).toContainText(pages.carManagementPage.CAR_MESSAGES.DELETED_SUCCESS);
        await expect(await pages.carManagementPage.getCarRow(car)).toBeNull();
    })
});
