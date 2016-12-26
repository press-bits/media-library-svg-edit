<?php

namespace PressBits\UnitTests;

use PHPUnit_Framework_TestCase;
use Brain\Monkey;

class WpTestCase extends PHPUnit_Framework_TestCase
{

    protected function setUp()
    {
        parent::setUp();
        Monkey::setUpWP();
    }

    protected function tearDown()
    {
        Monkey::tearDownWP();
        parent::tearDown();
    }
}