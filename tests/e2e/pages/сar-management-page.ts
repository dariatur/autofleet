import { Page, Locator, expect } from "@playwright/test";

import { PageObject } from "./page-object.js";
import { Car } from '../entity/car.js';
import { NewCarModalPage } from './new-car-modal-page';
import { EditCarModalPage } from './edit-car-modal-page';

export class CarManagementPage extends PageObject {
    readonly addNewCarButton: Locator = this.page.getByRole("button", { name: "Add New Car" });
    readonly alertMessage: Locator = this.page.getByRole("alert");
    readonly tableRows: Locator = this.page.locator('tbody tr');
    readonly nextButton: Locator = this.page.getByRole('button', { name: 'Next page' });
    readonly CAR_MESSAGES = {
        ADDED_SUCCESS: "Car added successfully!",
        UPDATED_SUCCESS: "Car updated successfully!",
        DELETED_SUCCESS: "Car deleted successfully!",
    };
    readonly newCarModalPage: NewCarModalPage = new NewCarModalPage(this.page);
    readonly editCarModalPage: EditCarModalPage = new EditCarModalPage(this.page);
    readonly deleteCarModal = this.page.locator('div.modal.fade').filter({
        hasText: 'Confirm Delete'
    });
    readonly editCarModal = this.page.locator('div.modal.fade').filter({
        has: this.page.locator('.modal-title', { hasText: 'Edit Car' })
    });


    constructor(page: Page) {
        super(page);
    }

    async clickOnAddNewCarButton() {
        await this.addNewCarButton.click();
    }

    async clickOnEditButton(car: Car) {
        const priceText = '$' + car.price.toLocaleString('en-US');
        const row = this.tableRows
            .filter({ hasText: car.make })
            .filter({ hasText: car.model })
            .filter({ hasText: car.year.toString() })
            .filter({ hasText: priceText });
        row.getByRole('button').nth(0).click();
        
        await expect(this.editCarModal).toHaveClass(/show/, { timeout: 5000 });
    }

    async getCarRow(car: Car): Promise<Locator | null> {
        this.page.waitForLoadState('load');
        const priceText = '$' + car.price.toLocaleString('en-US');
        while (true) {
            const rows = this.tableRows
                .filter({ hasText: car.make })
                .filter({ hasText: car.model })
                .filter({ hasText: car.year.toString() })
                .filter({ hasText: priceText });
    
            if (await rows.count() > 0) {
                return rows.first();
            }
            
            if ((await this.nextButton.count()) === 0) break;
            
            if (!(await this.nextButton.isDisabled())) {
                await this.nextButton.click();
                await this.page.waitForLoadState('networkidle');
            } else {
                break;
            }
        }
    
        return null;
    }

    async deleteCar(car: Car) {
        const priceText = '$' + car.price.toLocaleString('en-US');
        await this.tableRows
            .filter({ hasText: car.make })
            .filter({ hasText: car.model })
            .filter({ hasText: car.year.toString() })
            .filter({ hasText: priceText })
            .locator('button.btn-outline-danger').click(); //добавить модалку
        
        await expect(this.deleteCarModal).toBeVisible({ timeout: 5000 });
        await this.page.getByRole('button', { name: 'Delete Car' }).click(); 
        await expect(this.deleteCarModal).toBeHidden({ timeout: 5000 });
    }
}
