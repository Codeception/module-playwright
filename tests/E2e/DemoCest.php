<?php

namespace E2e;

use \E2eTester;

class DemoCest
{
    public function _before(E2eTester $I)
    {
    }

    // tests
    public function tryToTest(E2eTester $I)
    {
        $I->amOnPage('/');
        $I->see('Welcome');
        // $I->see('Welcome back');
    }
}
