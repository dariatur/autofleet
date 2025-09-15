import { Page, Locator } from '@playwright/test';

import { PageElement } from './page-element';
import { Car } from '../entity/car';

export class EditCarModalPage extends PageElement {
    readonly makeInput: Locator = this.page.locator("#make");
    readonly modelInput: Locator = this.page.locator("#model");
    readonly yearInput: Locator = this.page.locator("#year");
    readonly priceInput: Locator = this.page.locator("#price");
    readonly saveButton: Locator = this.page.getByRole("button", { name: "Update Car" });

    constructor(page: Page) {
        super(page);
    }

    async updateCar(car: Car): Promise<void> {
        await this.makeInput.fill(car.make);
        await this.modelInput.fill(car.model);
        await this.yearInput.fill(car.year.toString());
        await this.priceInput.fill(car.price.toString());
        await this.saveButton.click();
        await this.page.waitForLoadState('load');
    }
}