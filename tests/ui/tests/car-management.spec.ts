import { expect } from "@playwright/test";

import { test } from '../fixture';
import { CarFactory } from '../entity/car-factory';


const factory = new CarFactory();
const cars = factory.createMany(10);
const car = factory.create();
// const cars = [
//     { make: 'Toyota', model: 'Camry', year: 2000, price: 20000 },
//     { make: 'Honda', model: 'Accord', year: 2010, price: 15000 },
//     { make: 'Ford', model: 'Mustang', year: 2015, price: 25000 },
// ];

test.describe('Car Management Page', () => {

    test.beforeEach(async ({ pages }) => {
        await pages.carManagementPage.navigateTo();
    });

    cars.forEach((car) => {
        test(`add new car: ${car.make} ${car.model}`, async ({ pages }) => {
            await pages.carManagementPage.clickOnAddNewCarButton();
            await pages.newCarModalPage.addCar(car);

            await expect(pages.carManagementPage.alertMessage).toContainText("Car added successfully!");
            expect(await pages.carManagementPage.getCarRow(car)).not.toBeNull();
        })
    });

    test(`delete car: ${car.make} ${car.model}`, async ({ pages }) => {
        await pages.carManagementPage.clearTable();
        await pages.carManagementPage.clickOnAddNewCarButton();
        await pages.newCarModalPage.addCar(car);

        await pages.carManagementPage.deleteCar(car);
        await expect(pages.carManagementPage.alertMessage).toContainText("Car deleted successfully!");
        expect(await pages.carManagementPage.getCarRow(car)).toBeNull();
    })
});
