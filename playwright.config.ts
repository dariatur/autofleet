import { defineConfig } from '@playwright/test';

export default defineConfig({
  testDir: './tests',
  use: {
    baseURL: 'http://plctest.ddev.site',
    headless: true,
    screenshot: 'only-on-failure',
    video: 'retain-on-failure',
  },
  reporter: [
    ['list'],
    ['allure-playwright']
  ],
  //retries: 1,
  projects: [
    { name: 'Chromium', use: { browserName: 'chromium' } },
  ]
});
