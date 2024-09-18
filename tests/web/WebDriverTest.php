<?php

declare(strict_types=1);

namespace Tests\Web;

use Codeception\Attribute\Skip;
use Codeception\Module\WebDriver;
use Codeception\Stub;
use Codeception\Stub\Expected;
use data;
use Facebook\WebDriver\Cookie;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverKeys;
use PHPUnit\Framework\Assert;
use Tests\Support\Helper\Playwright;

final class WebDriverTest extends TestsForBrowsers
{


    public const MODULE_CLASS = 'Codeception\Module\Playwright';

    public function _before()
    {
        $this->module = $this->getModule('Playwright');
    }

    public function _after()
    {
        data::clean();
    }

    public function testClickEventOnCheckbox()
    {
        $this->module->amOnPage('/form/checkbox');
        $this->module->uncheckOption('#checkin');
        $this->module->dontSee('ticked', '#notice');
        $this->module->checkOption('#checkin');
        $this->module->see('ticked', '#notice');
    }

    public function testAcceptPopup()
    {
        $this->notForPhantomJS();
        $this->module->amOnPage('/form/popup');
        $this->module->click('Confirm');
        $this->module->acceptPopup();
        $this->module->see('Yes', '#result');
    }

    public function testSelectByCss()
    {
        $this->module->amOnPage('/form/select');
        $this->module->selectOption('form select[name=age]', '21-60');
        $this->module->click('Submit');
        $form = data::get('form');
        $this->assertSame('adult', $form['age']);
    }

    public function testSelectInvalidOptionForSecondSelectFails()
    {
        $this->shouldFail();
        $this->module->amOnPage('/form/select_second');
        $this->module->selectOption('#select2', 'Value2');
    }

    #[Skip]
    public function testSeeInPopup()
    {
        $this->notForPhantomJS();
        $this->module->amOnPage('/form/popup');
        $this->module->click('Alert');
        $this->module->seeInPopup('Really?');
        $this->module->cancelPopup();
    }

    #[Skip]
    public function testFailedSeeInPopup()
    {
        $this->notForPhantomJS();
        $this->expectException('\PHPUnit\Framework\AssertionFailedError');
        $this->expectExceptionMessage('Failed asserting that \'Really?\' contains "Different text"');
        $this->module->amOnPage('/form/popup');
        $this->module->click('Alert');
        $this->module->seeInPopup('Different text');
        $this->module->cancelPopup();
    }

    #[Skip]
    public function testDontSeeInPopup()
    {
        $this->notForPhantomJS();
        $this->module->amOnPage('/form/popup');
        $this->module->click('Alert');
        $this->module->dontSeeInPopup('Different text');
        $this->module->cancelPopup();
    }

    #[Skip]
    public function testFailedDontSeeInPopup()
    {
        $this->notForPhantomJS();
        $this->expectException('\PHPUnit\Framework\AssertionFailedError');
        $this->expectExceptionMessage('Failed asserting that \'Really?\' does not contain "Really?"');
        $this->module->amOnPage('/form/popup');
        $this->module->click('Alert');
        $this->module->dontSeeInPopup('Really?');
        $this->module->cancelPopup();
    }


    public function testScreenshot()
    {
        $this->module->amOnPage('/');
        @unlink(\Codeception\Configuration::outputDir() . 'testshot.png');

        $this->module->makeScreenshot('testshot.png');
        $this->assertFileExists(\Codeception\Configuration::outputDir() . 'testshot.png');
    }

    public function testElementScreenshot()
    {
        $this->module->amOnPage('/');
        $testName = "debugTestElement.png";

        $this->module->makeElementScreenshot('#area4', $testName);
        $this->assertFileExists(\Codeception\Configuration::outputDir() . $testName);
        @unlink(\Codeception\Configuration::outputDir() . $testName);
    }

    /**
     * @env chrome
     */
    public function testKeys()
    {
        $this->module->amOnPage('/form/field');
        $this->module->pressKey('#name', ['ctrl', 'a'], WebDriverKeys::DELETE);
        $this->module->pressKey('#name', 'test', ['shift', '111']);
        $this->module->pressKey('#name', '1');
        $this->module->seeInField('#name', 'test!!!1');
    }

    public function testWait()
    {
        $this->module->amOnPage('/');
        $time = time();
        $this->module->wait(3);
        $this->assertGreaterThanOrEqual($time + 3, time());
    }


    public function testSelectInvalidOptionFails()
    {
        $this->shouldFail();
        $this->module->amOnPage('/form/select');
        $this->module->selectOption('#age', '13-22');
    }

    public function testTypeOnTextarea()
    {
        $this->module->amOnPage('/form/textarea');
        $this->module->fillField('form #description', '');
        $this->module->type('Hello world');
        $this->module->click('Submit');
        $form = data::get('form');
        $this->assertSame('Hello world', $form['description']);
    }

    public function testAppendFieldTextareaFails()
    {
        $this->shouldFail();
        $this->module->amOnPage('/form/textarea');
        $this->module->appendField('form #description123', ' code');
    }

    public function testAppendFieldText()
    {
        $this->module->amOnPage('/form/field');
        $this->module->appendField('form #name', ' code');
        $this->module->click('Submit');
        $form = data::get('form');
        $this->assertSame('OLD_VALUE code', $form['name']);
    }

    public function testTypeOnTextField()
    {

        $this->module->amOnPage('/form/field');
        $this->module->fillField('form #name', '');
        $this->module->type('Hello world');
        $this->module->click('Submit');
        $form = data::get('form');
        $this->assertSame('Hello world', $form['name']);
    }

    public function testAppendFieldTextFails()
    {
        $this->shouldFail();
        $this->module->amOnPage('/form/field');
        $this->module->appendField('form #name123', ' code');
    }

    public function testSeeVisible()
    {
        $this->module->amOnPage('/info');
        $this->module->dontSee('Invisible text');
        $this->module->seeInPageSource('Invisible text');
    }

    public function testSeeInvisible()
    {
        $this->shouldFail();
        $this->module->amOnPage('/info');
        $this->module->see('Invisible text');
    }

    // fails in PhpBrowser :(
    public function testSubmitUnchecked()
    {
        $this->module->amOnPage('/form/unchecked');
        $this->module->seeCheckboxIsChecked('#checkbox');
        $this->module->uncheckOption('#checkbox');
        $this->module->click('#submit');
        ;
        $this->module->see('0', '#notice');
    }

    protected function notForPhantomJS()
    {
        return false;
    }

    protected function notForSelenium()
    {
        return false;
    }

    public function testScrollTo()
    {
        $this->module->amOnPage('/form/example18');
        $this->module->scrollTo('#clickme');
        $this->module->click('Submit');
        $this->module->see('Welcome to test app!');
    }

    /**
     * @Issue 2921
     */
    public function testSeeInFieldForTextarea()
    {
        $this->module->amOnPage('/form/bug2921');
        $this->module->seeInField('foo', 'bar baz');
    }

    /**
    * @Issue 4726
    */
    public function testClearField()
    {
        $this->module->amOnPage('/form/textarea');
        $this->module->fillField('#description', 'description');
        $this->module->clearField('#description');
        $this->module->dontSeeInField('#description', 'description');
    }

    public function testClickHashLink()
    {
        $this->module->amOnPage('/form/anchor');
        $this->module->click('Hash Link');
        $this->module->seeCurrentUrlEquals('/form/anchor#b');
    }

    /**
     * @Issue 3865
     */
    public function testClickNumericLink()
    {
        $this->module->amOnPage('/form/bug3865');
        $this->module->click('222');
        $this->module->see('Welcome to test app');
    }

    public function testClickHashButton()
    {
        $this->module->amOnPage('/form/anchor');
        $this->module->click('Hash Button');
        $this->module->seeCurrentUrlEquals('/form/anchor#c');
    }

    public function testSubmitHashForm()
    {
        $this->module->amOnPage('/form/anchor');
        $this->module->click('Hash Form');
        $this->module->seeCurrentUrlEquals('/form/anchor#a');
    }

    public function testSubmitHashFormTitle()
    {
        $this->module->amOnPage('/form/anchor');
        $this->module->click('Hash Form Title');
        $this->module->seeCurrentUrlEquals('/form/anchor#a');
    }

    public function testSubmitHashButtonForm()
    {
        $this->module->amOnPage('/form/anchor');
        $this->module->click('Hash Button Form');
        $this->module->seeCurrentUrlEquals('/form/anchor#a');
    }

    /**
     * @group window
     * @env chrome
     */
    public function testMoveMouseOver()
    {
        $this->module->amOnPage('/form/click');

        $this->module->moveMouseOver(null, 123, 88);
        $this->module->clickWithLeftButton(null, 0, 0);
        $this->module->see('click, offsetX: 123 - offsetY: 88');

        $this->module->moveMouseOver(null, 10, 10);
        $this->module->clickWithLeftButton(null, 0, 0);
        $this->module->see('click, offsetX: 133 - offsetY: 98');

        $this->module->moveMouseOver('#element2');
        $this->module->clickWithLeftButton(null, 0, 0);
        $this->module->see('click, offsetX: 58 - offsetY: 158');

        $this->module->moveMouseOver('#element2', 0, 0);
        $this->module->clickWithLeftButton(null, 0, 0);
        $this->module->see('click, offsetX: 8 - offsetY: 108');
    }

    /**
     * @group window
     * @env chrome
     */
    public function testLeftClick()
    {
        $this->module->amOnPage('/form/click');

        $this->module->clickWithLeftButton(null, 123, 88);
        $this->module->see('click, offsetX: 123 - offsetY: 88');

        $this->module->clickWithLeftButton('body');
        $this->module->see('click, offsetX: 600 - offsetY: 384');

        $this->module->clickWithLeftButton('body', 50, 75);
        $this->module->see('click, offsetX: 58 - offsetY: 83');

        $this->module->clickWithLeftButton('body div');
        $this->module->see('click, offsetX: 58 - offsetY: 58');

        $this->module->clickWithLeftButton('#element2', 70, 75);
        $this->module->see('click, offsetX: 78 - offsetY: 183');
    }

    /**
     * @group window
     * @env chrome
     */
    public function testRightClick()
    {
        // actually not supported in phantomjs see https://github.com/ariya/phantomjs/issues/14005
        $this->notForPhantomJS();

        $this->module->amOnPage('/form/click');

        $this->module->clickWithRightButton(null, 123, 88);
        $this->module->see('context, offsetX: 123 - offsetY: 88');

        $this->module->clickWithRightButton('body');
        $this->module->see('context, offsetX: 600 - offsetY: 384');

        $this->module->clickWithRightButton('body', 50, 75);
        $this->module->see('context, offsetX: 58 - offsetY: 83');

        $this->module->clickWithRightButton('body div');
        $this->module->see('context, offsetX: 58 - offsetY: 58');

        $this->module->clickWithRightButton('#element2', 70, 75);
        $this->module->see('context, offsetX: 78 - offsetY: 183');
    }

//    #[Skip('Breaks other tests')]
    public function testBrowserTabs()
    {
        $this->notForPhantomJS();
        $this->module->amOnPage('/form/example1');
        $this->module->openNewTab();
        $this->module->amOnPage('/form/example2');
        $this->module->openNewTab();
        $this->module->amOnPage('/form/example3');
        $this->module->openNewTab();
        $this->module->amOnPage('/form/example4');
        $this->module->openNewTab();
        $this->module->amOnPage('/form/example5');
        $this->module->closeTab();
        $this->module->seeInCurrentUrl('example4');
        $this->module->switchToPreviousTab(2);
        $this->module->seeInCurrentUrl('example2');
        $this->module->switchToNextTab();
        $this->module->seeInCurrentUrl('example3');
        $this->module->closeTab();
        $this->module->seeInCurrentUrl('example2');
        $this->module->seeNumberOfTabs(3);
        $this->module->closeTab();
        $this->module->seeNumberOfTabs(2);
        $this->module->closeOtherTabs();
    }

    #[Skip('Should work but not works')]
    public function testSwitchToIframe()
    {
        $this->module->amOnPage('iframe');
        $this->module->switchToIFrame('iframe');
        $this->module->see('Lots of valuable data here');
        $this->module->switchToIFrame();
        $this->module->see('Iframe test');
        $this->module->switchToIFrame("//iframe[@name='content']");
        $this->module->see('Lots of valuable data here');
    }


    public function testGrabPageSourceWhenOnPage()
    {
        $this->module->amOnPage('/minimal');
        $sourceExpected =
        <<<HTML
<!DOCTYPE html>
<html>
    <head>
        <title>
            Minimal page
        </title>
    </head>
    <body>
        <h1>
            Minimal page
        </h1>
    </body>
</html>

HTML
        ;
        $sourceActualRaw = $this->module->grabPageSource();
        // `Selenium` adds the `xmlns` attribute while `PhantomJS` does not do that.
        $sourceActual = str_replace('xmlns="http://www.w3.org/1999/xhtml"', '', $sourceActualRaw);
        $this->assertXmlStringEqualsXmlString($sourceExpected, $sourceActual);
    }

}
