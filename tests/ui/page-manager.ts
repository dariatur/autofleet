import { Page } from '@playwright/test';

import { CarManagementPage } from './pages/сar-management-page';
import { NewCarModalPage } from './pages/new-car-modal-page';

export class PageManager {
    readonly page: Page;
    readonly carManagementPage: CarManagementPage;
    readonly newCarModalPage: NewCarModalPage;

    constructor(page: Page) {
        this.page = page;
        this.carManagementPage = new CarManagementPage(page);
        this.newCarModalPage = new NewCarModalPage(page);
    }
}