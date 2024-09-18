<?php
namespace Codeception\Module;

use Codeception\Exception\ContentNotFound;
use Codeception\Exception\ModuleException;
use Codeception\Module;
use PHPUnit\Framework\AssertionFailedError;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

/**
 * This module uses Playwright to interact with a browser.
 *
 * ## Configuration
 *
 * * url: (string) Base URL of the application under test.
 * * browser: (string) Browser to use for testing. Possible values: chromium, firefox, webkit. Default is chromium.
 * * show: (bool) Show browser window. Default is false.
 * * host: (string) Hostname for the Playwright server.
 * * port: (int) Port for the Playwright server.
 */
class Playwright extends Module
{
    protected array $config = [
        'pw_server' => 'http://localhost:8191',
        'pw_start' => true,
        'pw_debug' => false,
        'timeout' => 5000,
        'url' => '',
        'browser' => 'chromium',
        'show' => true,
    ];

    const string NPM_PACKAGE = 'codeception-module-playwright';
    protected ?Process $serverProcess = null;

    public function _initialize()
    {
        if ($this->config['pw_start']) {

            $process = new Process(['npx', self::NPM_PACKAGE]);
            $process->start();

            sleep(2);

            if (!$process->isRunning() && !$process->isSuccessful()) {
                throw new ProcessFailedException($process);
            }

            $this->serverProcess = $process;
        }

        $this->config['output_dir'] = codecept_output_dir();
        $this->config['data_dir'] = codecept_data_dir();
        $this->sendCommand('', $this->config, '/init');
    }

    public function __destruct()
    {
        if ($this->serverProcess) {
            $this->serverProcess->stop();
        }
    }
    public function _afterSuite()
    {
        if ($this->serverProcess) {
            $this->debug($this->serverProcess->getOutput());
            $this->serverProcess->stop();
        }
    }

    /**
     * Constructor for the Playwright module.
     *
     * @param array $config Configuration for the module.
     */
    public function _beforeSuite(array $settings = [])
    {
        $this->sendCommand('_beforeSuite');
    }

    /**
     * Initialize the module.
     */
    public function _init(): void
    {
        $this->sendCommand('_init');
    }

    /**
     * Execute actions before a test.
     *
     * @param mixed $test The test case.
     */
    public function _before(mixed $test): void
    {
        $this->sendCommand('_before', [$test]);
    }

    /**
     * Execute actions after a test.
     */
    public function _after(mixed $test): void
    {
        $this->sendCommand('_after');
    }

    public function _restart()
    {
        return $this->sendCommand('_restart');
    }

    public function _finishTest($test)
    {
        return $this->sendCommand('_finishTest', [$test]);
    }

    public function _getPageUrl()
    {
        return $this->sendCommand('_getPageUrl');
    }

    public function _startBrowser()
    {
        return $this->sendCommand('_startBrowser');
    }

    public function _stopBrowser()
    {
        return $this->sendCommand('_stopBrowser');
    }

    public function amOnSubdomain($url)
    {
        throw new ModuleException($this, "amOnSubdomain is not implemented, use \$I->amOnPage(\$url); instead");
    }

    public function seeOptionIsSelected($opt, $val)
    {
        throw new ModuleException($this, "seeOptionIsSelected is not implemented; use \$I->seeElement instead");
    }

    public function seeInFormFields()
    {
        throw new ModuleException($this, "seeInFormFields is not implemented; use \$I->seeElement instead");
    }

    public function seeLink()
    {
        throw new ModuleException($this, "seeLink is not implemented; use \$I->seeElement instead");
    }

    public function seeInSource($expected)
    {
        $this->sendCommand('seeInSource', [$expected]);
    }

    public function dontSeeInSource($expected)
    {
        $this->sendCommand('dontSeeInSource', [$expected]);
    }

    /**
     * {{> amOnPage }}
     */
    public function amOnPage(string $url): void
    {
        $this->sendCommand('amOnPage', [$url]);
    }

    /**
     * Alias for amOnPage
     *
     * @param string $url
     * @return void
     */
    public function amOnUrl(string $url): void
    {
        $this->sendCommand('amOnPage', [$url]);
    }

    /**
     * {{> resizeWindow }}
     */
    public function resizeWindow(int $width, int $height): void
    {
        $this->sendCommand('resizeWindow', [$width, $height]);
    }

    /**
     * Set headers for all next requests
     *
     * ```php
     * $I->setPlaywrightRequestHeaders([
     *     'X-Sent-By' => 'CodeceptJS',
     * ]);
     * ```
     *
     * @param array $customHeaders headers to set
     */
    public function setPlaywrightRequestHeaders(array $customHeaders): void
    {
        $this->sendCommand('setPlaywrightRequestHeaders', [$customHeaders]);
    }

    public function moveMouseOver($locator, $offsetX = 0, $offsetY = 0)
    {
        return $this->sendCommand('moveCursorTo', [$locator, $offsetX, $offsetY]);
    }

    public function focus($locator, $options = [])
    {
        return $this->sendCommand('focus', [$locator, $options]);
    }

    public function blur($locator, $options = [])
    {
        return $this->sendCommand('blur', [$locator, $options]);
    }

    public function grabCheckedElementStatus($locator, $options = [])
    {
        return $this->sendCommand('grabCheckedElementStatus', [$locator, $options]);
    }

    public function grabDisabledElementStatus($locator, $options = [])
    {
        return $this->sendCommand('grabDisabledElementStatus', [$locator, $options]);
    }

    public function dragAndDrop($srcElement, $destElement, $options = null)
    {
        return $this->sendCommand('dragAndDrop', [$srcElement, $destElement, $options]);
    }

    public function restartBrowser($contextOptions = null)
    {
        return $this->sendCommand('restartBrowser', [$contextOptions]);
    }

    public function refreshPage()
    {
        return $this->sendCommand('refreshPage');
    }

    public function replayFromHar($harFilePath, $opts = null)
    {
        return $this->sendCommand('replayFromHar', [$harFilePath, $opts]);
    }

    public function scrollPageToTop()
    {
        return $this->sendCommand('scrollPageToTop');
    }

    public function scrollPageToBottom()
    {
        return $this->sendCommand('scrollPageToBottom');
    }

    public function scrollTo($locator, $offsetX = 0, $offsetY = 0)
    {
        return $this->sendCommand('scrollTo', [$locator, $offsetX, $offsetY]);
    }

    public function seeInTitle($text)
    {
        return $this->sendCommand('seeInTitle', [$text]);
    }

    public function grabPageScrollPosition()
    {
        return $this->sendCommand('grabPageScrollPosition');
    }

    public function seeTitleEquals($text)
    {
        return $this->sendCommand('seeTitleEquals', [$text]);
    }

    public function dontSeeInTitle($text)
    {
        return $this->sendCommand('dontSeeInTitle', [$text]);
    }

    public function grabTitle()
    {
        return $this->sendCommand('grabTitle');
    }

    public function switchToNextTab($num = 1)
    {
        return $this->sendCommand('switchToNextTab', [$num]);
    }

    public function switchToPreviousTab($num = 1)
    {
        return $this->sendCommand('switchToPreviousTab', [$num]);
    }

    public function closeTab()
    {
        return $this->sendCommand('closeCurrentTab');
    }

    public function closeOtherTabs()
    {
        return $this->sendCommand('closeOtherTabs');
    }

    public function openNewTab($options = null)
    {
        return $this->sendCommand('openNewTab', [$options]);
    }

    public function grabNumberOfOpenTabs()
    {
        return $this->sendCommand('grabNumberOfOpenTabs');
    }

    public function seeNumberOfTabs($expected)
    {
        $actual = $this->sendCommand('grabNumberOfOpenTabs');
        $this->assertEquals($expected, $actual, "Number of tabs expected $expected but actual $actual");
    }

    public function seeElement($locator, $attrs = null)
    {
        if ($attrs) {
            throw new ModuleException($this, "seeElement doesn't accept second param. Use CSS or XPath instead");
        }
        return $this->sendCommand('seeElement', [$locator]);
    }

    public function dontSeeElement($locator, $attrs = null)
    {
        if ($attrs) {
            throw new ModuleException($this, "seeElement doesn't accept second param. Use CSS or XPath instead");
        }

        return $this->sendCommand('dontSeeElement', [$locator]);
    }

    public function seeElementInDOM($locator)
    {
        return $this->sendCommand('seeElementInDOM', [$locator]);
    }

    public function dontSeeElementInDOM($locator)
    {
        return $this->sendCommand('dontSeeElementInDOM', [$locator]);
    }

    public function handleDownloads($fileName)
    {
        return $this->sendCommand('handleDownloads', [$fileName]);
    }

    public function click($locator, $context = null, $options = [])
    {
        return $this->sendCommand('click', [$locator, $context, $options]);
    }

    public function clickWithLeftButton($locator, $x, $y)
    {
        return $this->sendCommand('click', [$locator, null, ['position' => ['x' => $x, 'y' => $y]]]);
    }

    public function clickWithRightButton($locator, $x, $y)
    {
        return $this->sendCommand('click', [$locator, null, ['button' => 'right', 'position' => ['x' => $x, 'y' => $y]]]);
    }

    public function forceClick($locator, $context = null)
    {
        return $this->sendCommand('forceClick', [$locator, $context]);
    }

    public function doubleClick($locator, $context = null)
    {
        return $this->sendCommand('doubleClick', [$locator, $context]);
    }

    public function rightClick($locator, $context = null)
    {
        return $this->sendCommand('rightClick', [$locator, $context]);
    }

    public function checkOption($field, $context = null, $options = ['force' => true])
    {
        return $this->sendCommand('checkOption', [$field, $context, $options]);
    }

    public function uncheckOption($field, $context = null, $options = ['force' => true])
    {
        return $this->sendCommand('uncheckOption', [$field, $context, $options]);
    }

    public function seeCheckboxIsChecked($field)
    {
        return $this->sendCommand('seeCheckboxIsChecked', [$field]);
    }

    public function dontSeeCheckboxIsChecked($field)
    {
        return $this->sendCommand('dontSeeCheckboxIsChecked', [$field]);
    }

    public function pressKeyDown($key)
    {
        return $this->sendCommand('pressKeyDown', [$key]);
    }

    public function pressKeyUp($key)
    {
        return $this->sendCommand('pressKeyUp', [$key]);
    }

    public function pressKey($key)
    {
        return $this->sendCommand('pressKey', [$key]);
    }

    public function type($keys, $delay = null)
    {
        return $this->sendCommand('type', [$keys, $delay]);
    }

    public function fillField($field, $value)
    {
        return $this->sendCommand('fillField', [$field, $value]);
    }

    public function clearField($locator, $options = [])
    {
        return $this->sendCommand('clearField', [$locator, $options]);
    }

    public function appendField($field, $value)
    {
        return $this->sendCommand('appendField', [$field, $value]);
    }

    public function seeInField($field, $value)
    {
        return $this->sendCommand('seeInField', [$field, $value]);
    }

    public function dontSeeInField($field, $value)
    {
        return $this->sendCommand('dontSeeInField', [$field, $value]);
    }

    public function attachFile($locator, $pathToFile)
    {
        return $this->sendCommand('attachFile', [$locator, $pathToFile]);
    }

    public function selectOption($select, $option)
    {
        return $this->sendCommand('selectOption', [$select, $option]);
    }

    public function grabNumberOfVisibleElements($locator)
    {
        return $this->sendCommand('grabNumberOfVisibleElements', [$locator]);
    }

    public function seeInCurrentUrl($uri)
    {
        return $this->sendCommand('seeInCurrentUrl', [$uri]);
    }

    public function dontSeeInCurrentUrl($uri)
    {
        return $this->sendCommand('dontSeeInCurrentUrl', [$uri]);
    }

    public function seeCurrentUrlEquals($uri)
    {
        return $this->sendCommand('seeCurrentUrlEquals', [$uri]);
    }

    public function dontSeeCurrentUrlEquals($uri)
    {
        return $this->sendCommand('dontSeeCurrentUrlEquals', [$uri]);
    }

    public function see($text, $context = null)
    {
        return $this->sendCommand('see', [$text, $context]);
    }

    public function seeTextEquals($text, $context = null)
    {
        return $this->sendCommand('seeTextEquals', [$text, $context]);
    }

    public function dontSee($text, $context = null)
    {
        return $this->sendCommand('dontSee', [$text, $context]);
    }

    public function grabPageSource()
    {
        return $this->sendCommand('grabSource');
    }

    public function grabBrowserLogs()
    {
        return $this->sendCommand('grabBrowserLogs');
    }

    public function grabCurrentUrl()
    {
        return $this->sendCommand('grabCurrentUrl');
    }

    public function seeInPageSource($raw)
    {
        return $this->sendCommand('seeInSource', [$raw]);
    }

    public function dontSeeInPageSource($raw)
    {
        return $this->sendCommand('dontSeeInSource', [$raw]);
    }

    public function seeNumberOfElements($selector, $expected)
    {
        return $this->sendCommand('seeNumberOfElements', [$selector, $expected]);
    }

    public function seeNumberOfVisibleElements($locator, $num)
    {
        return $this->sendCommand('seeNumberOfVisibleElements', [$locator, $num]);
    }

    public function setCookie($cookie, $value = null)
    {
        if (is_string($cookie) && $value) {
            $cookie = ['name' => $cookie, 'value' => $value, 'url' => $this->config['url']];
        }
        return $this->sendCommand('setCookie', [$cookie]);
    }

    public function resetCookie($cookie = null)
    {
        return $this->sendCommand('clearCookie', [$cookie]);
    }

    public function seeCookie($name)
    {
        return $this->sendCommand('seeCookie', [$name]);
    }

    public function dontSeeCookie($name)
    {
        return $this->sendCommand('dontSeeCookie', [$name]);
    }

    public function grabCookie($name)
    {
        $cookie = $this->sendCommand('grabCookie', [$name]);
        if (is_array($cookie)) {
            return $cookie['value'];
        }
        return $cookie;
    }

    public function clearCookie()
    {
        return $this->sendCommand('clearCookie');
    }

    public function executeScript($fn, $arg = null)
    {
        return $this->sendCommand('executeScript', [$fn, $arg]);
    }

    public function grabTextFrom($locator)
    {
        return $this->sendCommand('grabTextFrom', [$locator]);
    }

    public function grabMultiple($locator, $attribute = null)
    {
        if ($attribute) {
            return $this->sendCommand('grabAttributeFromAll', [$locator, $attribute]);
        }
        return $this->sendCommand('grabTextFromAll', [$locator]);
    }

    public function grabValueFrom($locator)
    {
        return $this->sendCommand('grabValueFrom', [$locator]);
    }

    public function grabValueFromAll($locator)
    {
        return $this->sendCommand('grabValueFromAll', [$locator]);
    }

    public function grabHTMLFrom($locator)
    {
        return $this->sendCommand('grabHTMLFrom', [$locator]);
    }

    public function grabHTMLFromAll($locator)
    {
        return $this->sendCommand('grabHTMLFromAll', [$locator]);
    }

    public function grabCssPropertyFrom($locator, $cssProperty)
    {
        return $this->sendCommand('grabCssPropertyFrom', [$locator, $cssProperty]);
    }

    public function grabCssPropertyFromAll($locator, $cssProperty)
    {
        return $this->sendCommand('grabCssPropertyFromAll', [$locator, $cssProperty]);
    }

    public function seeCssPropertiesOnElements($locator, $cssProperties)
    {
        return $this->sendCommand('seeCssPropertiesOnElements', [$locator, $cssProperties]);
    }

    public function seeAttributesOnElements($locator, $attributes)
    {
        return $this->sendCommand('seeAttributesOnElements', [$locator, $attributes]);
    }

    public function dragSlider($locator, $offsetX = 0)
    {
        return $this->sendCommand('dragSlider', [$locator, $offsetX]);
    }

    public function grabAttributeFrom($locator, $attr)
    {
        return $this->sendCommand('grabAttributeFrom', [$locator, $attr]);
    }

    public function grabAttributeFromAll($locator, $attr)
    {
        return $this->sendCommand('grabAttributeFromAll', [$locator, $attr]);
    }

    public function makeElementScreenshot($locator, $fileName)
    {
        return $this->sendCommand('saveElementScreenshot', [$locator, $fileName]);
    }

    public function makeScreenshot($fileName, $fullPage = null)
    {
        return $this->sendCommand('saveScreenshot', [$fileName, $fullPage]);
    }

    public function makeApiRequest($method, $url, $options = null)
    {
        return $this->sendCommand('makeApiRequest', [$method, $url, $options]);
    }

    public function wait($sec)
    {
        return $this->sendCommand('wait', [$sec]);
    }

    public function waitForEnabled($locator, $sec = null)
    {
        return $this->sendCommand('waitForEnabled', [$locator, $sec]);
    }

    public function waitForDisabled($locator, $sec = null)
    {
        return $this->sendCommand('waitForDisabled', [$locator, $sec]);
    }

    public function waitForValue($field, $value, $sec = null)
    {
        return $this->sendCommand('waitForValue', [$field, $value, $sec]);
    }

    public function waitNumberOfVisibleElements($locator, $num, $sec = null)
    {
        return $this->sendCommand('waitNumberOfVisibleElements', [$locator, $num, $sec]);
    }

    public function waitForElement($locator, $sec = null)
    {
        return $this->sendCommand('waitForElement', [$locator, $sec]);
    }

    public function waitForVisible($locator, $sec = null)
    {
        return $this->sendCommand('waitForVisible', [$locator, $sec]);
    }

    public function waitForInvisible($locator, $sec = null)
    {
        return $this->sendCommand('waitForInvisible', [$locator, $sec]);
    }

    public function waitToHide($locator, $sec = null)
    {
        return $this->sendCommand('waitToHide', [$locator, $sec]);
    }

    public function waitForText($text, $sec = null, $context = null)
    {
        return $this->sendCommand('waitForText', [$text, $sec, $context]);
    }

    public function waitForRequest($urlOrPredicate, $sec = null)
    {
        return $this->sendCommand('waitForRequest', [$urlOrPredicate, $sec]);
    }

    public function waitForResponse($urlOrPredicate, $sec = null)
    {
        return $this->sendCommand('waitForResponse', [$urlOrPredicate, $sec]);
    }

    public function switchToIFrame($locator = null)
    {
        return $this->sendCommand('switchTo', [$locator]);
    }

    public function waitForFunction($fn, $argsOrSec = null, $sec = null)
    {
        return $this->sendCommand('waitForFunction', [$fn, $argsOrSec, $sec]);
    }

    public function waitForNavigation($options = [])
    {
        return $this->sendCommand('waitForNavigation', [$options]);
    }

    public function waitForURL($url, $options = [])
    {
        return $this->sendCommand('waitForURL', [$url, $options]);
    }

    public function waitForDetached($locator, $sec = null)
    {
        return $this->sendCommand('waitForDetached', [$locator, $sec]);
    }

    public function waitForCookie($name, $sec = null)
    {
        return $this->sendCommand('waitForCookie', [$name, $sec]);
    }

    public function grabDataFromPerformanceTiming()
    {
        return $this->sendCommand('grabDataFromPerformanceTiming');
    }

    public function grabElementBoundingRect($locator, $prop)
    {
        return $this->sendCommand('grabElementBoundingRect', [$locator, $prop]);
    }

    public function mockRoute($url, $handler = null)
    {
        return $this->sendCommand('mockRoute', [$url, $handler]);
    }

    public function stopMockingRoute($url, $handler = null)
    {
        return $this->sendCommand('stopMockingRoute', [$url, $handler]);
    }

    public function startRecordingTraffic()
    {
        return $this->sendCommand('startRecordingTraffic');
    }

    public function blockTraffic($urls)
    {
        return $this->sendCommand('blockTraffic', [$urls]);
    }

    public function mockTraffic($urls, $responseString, $contentType = 'application/json')
    {
        return $this->sendCommand('mockTraffic', [$urls, $responseString, $contentType]);
    }

    public function flushNetworkTraffics()
    {
        return $this->sendCommand('flushNetworkTraffics');
    }

    public function stopRecordingTraffic()
    {
        return $this->sendCommand('stopRecordingTraffic');
    }

    public function grabTrafficUrl($urlMatch)
    {
        return $this->sendCommand('grabTrafficUrl', [$urlMatch]);
    }

    public function grabRecordedNetworkTraffics()
    {
        return $this->sendCommand('grabRecordedNetworkTraffics');
    }

    public function acceptPopup()
    {
        return $this->sendCommand('acceptPopup');
    }

    public function cancelPopup()
    {
        return $this->sendCommand('cancelPopup');
    }

    public function seeInPopup($text)
    {
        return $this->sendCommand('seeInPopup', [$text]);
    }

    public function seeTraffic($params)
    {
        return $this->sendCommand('seeTraffic', [$params]);
    }

    public function dontSeeTraffic($params)
    {
        return $this->sendCommand('dontSeeTraffic', [$params]);
    }

    public function startRecordingWebSocketMessages()
    {
        return $this->sendCommand('startRecordingWebSocketMessages');
    }

    public function stopRecordingWebSocketMessages()
    {
        return $this->sendCommand('stopRecordingWebSocketMessages');
    }

    public function grabWebSocketMessages()
    {
        return $this->sendCommand('grabWebSocketMessages');
    }

    public function flushWebSocketMessages()
    {
        return $this->sendCommand('flushWebSocketMessages');
    }

    public function grabMetrics()
    {
        return $this->sendCommand('grabMetrics');
    }

    /**
     * Send a command to the Playwright server.
     *
     * @param string $command The command to send.
     * @param array $arguments The arguments for the command.
     * @param string $endpoint The endpoint to send the command to.
     * @return mixed The response from the server.
     */
    private function sendCommand(string $command, array $arguments = [], string $endpoint = '/command'): mixed
    {
        $data = json_encode([
            'command' => $command,
            'arguments' => $arguments,
        ]);

        $url = $this->config['pw_server'];

        $ch = curl_init($url . $endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Accept: application/json',
            'Connection: close',
        ]);

        if (isset($arguments[0]) && is_string($arguments[0]) && str_starts_with($arguments[0], 'wait')) {
            // request with wait can take long time
            curl_setopt($ch, CURLOPT_TIMEOUT, $this->config['timeout'] * 100);
        } else {
            curl_setopt($ch, CURLOPT_TIMEOUT, $this->config['timeout']);
        }

        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

        if ($this->config['pw_debug']) {
            $this->debugSection('PW Command', $data);
        }

        $result = curl_exec($ch);

        if ($result === false) {
            $error = curl_error($ch);
            curl_close($ch);
            throw new ModuleException($this, "Playwright server did not respond. Error: $error");
        }

        $httpStatusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        if (!$httpStatusCode || $httpStatusCode === 500) {
            if ($this->config['pw_debug']) {
                $this->debugSection('PW Error', "Error response");
            }

            $json = json_decode($result, true);
            if (!isset($json['message'])) {
                throw new AssertionFailedError("Error for $command");
            }
            throw new AssertionFailedError($json['message']);
        }

        $json = json_decode($result, true);
        if (isset($json['result'])) {
            if ($this->config['pw_debug']) {
                $this->debugSection('PW Result', $json['result']);
            }

            return $json['result'];
        }

        return null;
    }
}
