import { Page, Locator, expect } from "@playwright/test";

import { PageObject } from "./page-object.js";
import { Car } from '../entity/car';

export class CarManagementPage extends PageObject {
    readonly addNewCarButton: Locator = this.page.getByRole("button", { name: "Add New Car" });
    readonly alertMessage: Locator = this.page.getByRole("alert");
    readonly tableRows: Locator = this.page.locator('tbody tr');
    readonly nextButton: Locator = this.page.getByRole('button', { name: 'Next page' });

    constructor(page: Page) {
        super(page);
    }

    async clickOnAddNewCarButton() {
        await this.addNewCarButton.click();
    }

    async getCarRow(car: Car): Promise<Locator | null> {
        const priceText = '$' + car.price.toLocaleString('en-US');
        while (true) {
            await this.page.waitForTimeout(200);
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

    async clearTable() {
        while (true) {
          const count = await this.tableRows.count();
          if (await this.page.getByText('No cars found.').isVisible()) break;
    
          const row = this.tableRows.nth(0);
          const deleteButton = row.locator('button.btn-outline-danger');
      
          await deleteButton.click();
      
          const confirmButton = this.page.getByRole('button', { name: 'Delete Car' });
          await confirmButton.click();
      
          await this.page.waitForTimeout(1000);
        }
      }

    async deleteCar(car: Car) {
        const priceText = '$' + car.price.toLocaleString('en-US');
        await this.tableRows
            .filter({ hasText: car.make })
            .filter({ hasText: car.model })
            .filter({ hasText: car.year.toString() })
            .filter({ hasText: priceText })
            .locator('button.btn-outline-danger').click();
        
        await this.page.getByRole('button', { name: 'Delete Car' }).click();
    }
}
