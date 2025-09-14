import { Page } from "@playwright/test";

export abstract class PageElement {
    readonly page: Page;

    constructor(page: Page) {
        this.page = page;
    }
}
