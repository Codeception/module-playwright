
# Playwright module for Codeception

🚀 This is the **first and the only module that allows [Playwright](https://playwright.dev) testing in PHP**. Playwright allows testing in Chrome, Firefox, Webkit (safari). It is faster and more reliable alternative to Selenium WebDriver. Finally it is available in PHP!

This module does not implement Playwright API in PHP, rather proxies requests to Playwright helper of [CodeceptJS](https://codecept.io). This is possible because Codeception and CodeceptJS share the same architecture principles, and the interface for web testing is quite the same.

> [!Note]
> This module can be used as a replacement for WebDriver module. Comparing to WebDriver module, Playwright module can **speed up your tests x3 times**, run in headless mode, record videos, and stack traces out of the box

## Requirements

* PHP 8.1+
* NodeJS 20+

## Installation

> [!Warning]
> This module is experimental. It is in early development stage. Please report any issues you find.

Install NodeJS part of this module, it will install Playwright and CodeceptJS

```
npm install codeception-module-playwright
```
Install Playwright browsers

```
npx playwright install --with-deps
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
Most common config values are:

* `url` - base url to open pages from
* `browser` - either `chromium`, `firefox`, `webkit`
* `show` - (default: `true`) to show browser or set to `false` to run tests in headless mode 
* `timeout` - (default: `5000`) timeout (in ms) for all Playwright operations
* `pw_server` - (default: 'http://localhost:8191') url of Playwright Server 
* `pw_debug` - (default: `false`) print Playwright Server debug information
* `video` - save video on fail
* `trace` - save traces on fail

More configuration options are is listed on [CodeceptJS Playwright page](https://codecept.io/helpers/Playwright/#configuration)


> [!Tip]
> This module is designed as drop-in WebDriver module replacement, you can change `WebDriver` to `Playwright` in your tests and try how it works!


## Usage

Playwright module requires NodeJS server to be running. Start the server manually:

```
npx codeception-playwright-module
```

If you want to start server automatically, use [RunProcess extension](https://codeception.com/extensions#RunProcess) from Codeception

```yaml
extensions:
    enabled:
        - Codeception\Extension\RunProcess:
            0: npx codeception-playwright-module
            sleep: 3 # wait 5 seconds for processes to boot
```

If you start server on different host or port, run port with `--port` option:

```
npx codeception-playwright-module --playwright 9999
```

And pass port to config

```yaml
modules:
    enabled:
        - Playwright:
            url: 'http://localhost'
            browser: 'chromium'
            show: true
            pw_server: http://otherhost:8191
```

## API

This module provides the same API as WebDriver module. You can try to use it in your tests without any changes.

For the full command list see [WebDriver module reference](https://codeception.com/docs/modules/WebDriver#dontSeeElement).

Playwright-specific commands are also available in [CodeceptJS Playwright Helper](https://codecept.io/helpers/Playwright/)

## Example

```php
$I->amOnPage('/');
$I->click('#first .clickable');
$I->dontSeeInTitle('Error');
$I->fillField('Username', 'John');
$I->fillField('Password', '1233456');
$I->see('Hello, world!');
```

## License MIT
