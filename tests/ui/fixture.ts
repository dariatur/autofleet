import { test as base, Page } from '@playwright/test';

import { PageManager } from './page-manager';


type Fixtures = {
  pages: PageManager;
};

export const test = base.extend<Fixtures>({
  pages: async ({ page }, use) => {
    await use(new PageManager(page));
  },
});


