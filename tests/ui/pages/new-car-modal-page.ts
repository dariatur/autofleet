import { Page, Locator } from "@playwright/test";

import { PageObject } from "./page-object";
import { Car } from '../entity/car';

export class NewCarModalPage extends PageObject {
    readonly makeInput: Locator = this.page.locator("#make");
    readonly modelInput: Locator = this.page.locator("#model");
    readonly yearInput: Locator = this.page.locator("#year");
    readonly priceInput: Locator = this.page.locator("#price");
    readonly addCarButton: Locator = this.page.getByRole("button", { name: "Add Car" });

    constructor(page: Page) {
        super(page);
    }

    async addCar(car: Car): Promise<void> {
        await this.makeInput.fill(car.make);
        await this.modelInput.fill(car.model);
        await this.yearInput.fill(car.year.toString());
        await this.priceInput.fill(car.price.toString());
        await this.addCarButton.click();
    }
}
