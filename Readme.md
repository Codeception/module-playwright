
# Playwright module for Codeception

🚀 This is the **first and the only module that allows [Playwright](https://playwright.dev) testing in PHP**. Playwright allows testing in Chrome, Firefox, Webkit (safari). It is faster and more reliable alternative to Selenium WebDriver from Microsoft. Finally it is available in PHP!

This module does not implement Playwright API in PHP, rather proxies requests to Playwright helper of [CodeceptJS](https://codecept.io). This is possible because Codeception and CodeceptJS share the same architecture principles and interface for web testing is quiet the same.

> [!Note]
> This module can be used as a replacement for WebDriver module. Comparing to WebDriver module, Playwright module can speed up your tests x3 times, run in headless mode, record videos, and stack traces out of the box

## Requirements

* PHP 8.1+
* NodeJS 20+

## Installation

> ![Warning]
> This module is experimental. It is in early development stage. Please report any issues you find.

Install NodeJS part of this module, it will install Playwright and CodeceptJS

```
npm install codeception-playwright-module
```
Install Playwright browsers

```
npx playwright install
```

Install PHP part of this module

```bash
composer require codeception/module-playwright --dev
```

## Configuration

Enable module in `codeception.yml`:

```yaml
modules:
    enabled:
        - Playwright:
            url: 'http://localhost'
            browser: 'chromium'
            show: true
```
> [!Tip]
> This module is designed as drop-in WebDriver module replacement, you can change `WebDriver` to `Playwright` in your tests and try how it works!

## Usage

Playwright module requires NodeJS server to be running. Playwright module will start it and stop automatically. Default port is **8191**.

If you want to disable automatic server start, set `start` option to `false`:

```yaml
modules:
    enabled:
        - Playwright:
            url: 'http://localhost'
            browser: 'chromium'
            show: true
            pw_start: false
```

In this case you can start the server manually:

```bash
npx codeception-playwright-module
```
Please check that server can be started with no issues.

If you start server on different host or port, you can configure it:

```yaml
modules:
    enabled:
        - Playwright:
            url: 'http://localhost'
            browser: 'chromium'
            show: true
            pw_start: false
            pw_server: http://otherhost:8191
```

## API

This module provides the same API as WebDriver module. You can try to use it in your tests without any changes.

Complete API reference is available in [CodeceptJS Playwright Helper](https://codecept.io/helpers/Playwright/)

## Example

```php
$I->amOnPage('/');
$I->dontSeeInTitle('Error');
$I->see('Hello, world!');
```

## License MIT
