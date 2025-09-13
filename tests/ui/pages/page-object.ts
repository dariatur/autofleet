import { Page } from "@playwright/test";

export abstract class PageObject {
    readonly page: Page;

    constructor(page: Page) {
        this.page = page;
    }

    async navigateTo(): Promise<void> {
        await this.page.goto('/');
        await this.page.waitForURL('/');
    }
}
