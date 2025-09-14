import { Page } from '@playwright/test';

import { CarManagementPage } from './pages/сar-management-page';

export class PageManager {
    readonly page: Page;
    readonly carManagementPage: CarManagementPage;

    constructor(page: Page) {
        this.page = page;
        this.carManagementPage = new CarManagementPage(page);
    }
}