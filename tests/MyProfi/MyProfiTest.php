<?php

namespace MyProfiTests;

/**
 * Class MyProfiTest
 * @package MyProfiTests
 */
class MyProfiTest extends \PHPUnit_Framework_TestCase
{
    /** @var \MyProfi\MyProfi */
    private $myprofi;

    public function setUp()
    {
        $this->myprofi = new \MyProfi\MyProfi();
        parent::setUp();
    }

    /**
     *
     */
    public function testCsVFilenameIsDetected()
    {
        $this->myprofi->setInputFile('foobar.csv');
        self::assertAttributeEquals(true, 'csv', $this->myprofi);
        self::assertAttributeEquals('foobar.csv', 'filename', $this->myprofi);
    }

    /**
     *
     */
    public function testNonCsvFilenameIsDetected()
    {
        $this->myprofi->setInputFile('foobar.log');
        self::assertAttributeEquals(false, 'csv', $this->myprofi);
        self::assertAttributeEquals('foobar.log', 'filename', $this->myprofi);
    }
}
