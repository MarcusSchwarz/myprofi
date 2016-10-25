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

    /**
     * Actually these do not test a "unit", but the whole process
     * I am sure this could be done better, but for the moment [tm] it is ok
     */
    private function setUpSimpleSlowYodaEventLog()
    {
        $this->myprofi->setInputFile(__DIR__ . '/../logs/slow_yoda_event.log');
        $this->myprofi->slow(true);
        $this->myprofi->processQueries();
    }

    /**
     * Actually these do not test a "unit", but the whole process
     * I am sure this could be done better, but for the moment [tm] it is ok
     */
    private function setUpPerconaStyleShortLog()
    {
        $this->myprofi->setInputFile(__DIR__ . '/../logs/percona_style_short.log');
        $this->myprofi->slow(true);
        $this->myprofi->processQueries();
    }

    public function testSimpleSlowYodaEventLogTotal()
    {
        $this->setUpSimpleSlowYodaEventLog();

        $totalNumberOfEntries = $this->myprofi->total();
        self::assertEquals(2, $totalNumberOfEntries);
    }

    public function testSimpleSlowYodaEventLogTypesStat()
    {
        $this->setUpSimpleSlowYodaEventLog();

        self::assertCount(1, $this->myprofi->getTypesStat());

        foreach ($this->myprofi->getTypesStat() as $type => $num) {
            self::assertEquals('select', $type);
            self::assertEquals(2, $num);
        }
    }

    public function testSimpleSlowYodaEventLogNums()
    {
        $this->setUpSimpleSlowYodaEventLog();

        $patternNums = $this->myprofi->getPatternNums();

        $expectedNums = [];
        $expectedNums['bec61a1e580942b2b0eb38cd4b5e9fc1'] = 1;
        $expectedNums['397ccc9858a34713edf005e9d92d5e64'] = 1;

        self::assertEquals($expectedNums, $patternNums);
    }

    public function testSimpleSlowYodaEventLogQueries()
    {
        $this->setUpSimpleSlowYodaEventLog();

        $patternQueries = $this->myprofi->getPatternQueries();

        $expectedQueries = [];
        $expectedQueries['bec61a1e580942b2b0eb38cd4b5e9fc1'] = 'select*from yoda_event;';
        $expectedQueries['397ccc9858a34713edf005e9d92d5e64'] = 'select*from yoda_event where location={};';

        self::assertEquals($expectedQueries, $patternQueries);
    }

    public function testPerconaStyleShortLogNums()
    {
        $this->setUpPerconaStyleShortLog();

        $patternNums = $this->myprofi->getPatternNums();

        $expectedNums = [];
        $expectedNums['bec61a1e580942b2b0eb38cd4b5e9fc1'] = 1;

        self::assertEquals($expectedNums, $patternNums);
    }

    public function testPerconaStyleShortLogQueries()
    {
        $this->setUpPerconaStyleShortLog();

        $patternQueries = $this->myprofi->getPatternQueries();

        $expectedQueries = [];
        $expectedQueries['bec61a1e580942b2b0eb38cd4b5e9fc1'] = 'select*from yoda_event;';

        self::assertEquals($expectedQueries, $patternQueries);
    }
}
