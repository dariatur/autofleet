import { APIRequestContext, expect } from '@playwright/test';
import { Car } from '../entity/car';

const CARS_URL = "/api/cars";

export class CarsApi {
    constructor(private api: APIRequestContext) {}

    async getAllCars() {
        const response = await this.api.get(CARS_URL);
        expect(response.status()).toBe(200);
        return response.json();
    }

    async deleteCars() {
        let response = await this.getAllCars();
        while(response.length > 0) {
            await Promise.all(response.map(car => this.deleteCar(car.id)));
            response = await this.getAllCars();
        }
    }

    async deleteCar(id: number) {
        const response = await this.api.delete(`${CARS_URL}/${id}`)
    }

    async createCar(car: Car) {
        const response = await this.api.post(CARS_URL, {
            data: car,
        });
        expect(response.status()).toBe(201);
        return await response.json();
    }
    
}