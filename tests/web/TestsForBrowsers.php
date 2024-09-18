<?php

declare(strict_types=1);

namespace Tests\Web;

/**
 * Author: davert
 * Date: 13.01.12
 *
 * Class TestsForMink
 * Description:
 *
 */
abstract class TestsForBrowsers extends TestsForWeb
{
    public function testOpenAbsoluteUrls()
    {
        $this->module->amOnUrl('http://localhost:8000/');
        $this->module->see('Welcome to test app!', 'h1');
        $this->module->amOnUrl('http://127.0.0.1:8000/info');
        $this->module->see('Information', 'h1');
        $this->module->amOnPage('/form/empty');
        $this->module->seeCurrentUrlEquals('/form/empty');
        $this->assertEquals('http://127.0.0.1:8000/form/empty', $this->module->grabCurrentUrl(), 'Host has changed');
    }

    public function testHeadersRedirect()
    {
        $this->module->amOnPage('/redirect');
        $this->module->seeInCurrentUrl('info');
    }

}
