import { test as base } from '@playwright/test';

import { PageManager } from './page-manager';
import { CarsApi } from './api/cars-api';


type Fixtures = {
  pages: PageManager;
  carsApi: CarsApi;
};

export const test = base.extend<Fixtures>({
  pages: async ({ page }, use) => {
    await use(new PageManager(page));
  },

  carsApi: async ({ request }, use) => {
    await use(new CarsApi(request));
  },
});


