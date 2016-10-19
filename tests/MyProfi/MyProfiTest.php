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
     * Actually this does not test a "unit", but the whole process
     * I am sure this could be done better, but for the moment [tm] it is ok
     */
    public function testSimpleSlowYodaEventLog()
    {
        $this->myprofi->setInputFile(__DIR__ . '/../logs/slow_yoda_event.log');
        $this->myprofi->slow(true);
        $this->myprofi->processQueries();

        $totalNumberOfEntries = $this->myprofi->total();
        self::assertEquals(2, $totalNumberOfEntries);

        self::assertCount(1, $this->myprofi->getTypesStat());

        foreach ($this->myprofi->getTypesStat() as $type => $num) {
            self::assertEquals('select', $type);
            self::assertEquals(2, $num);
        }

        $patternStats = $this->myprofi->getAllPatternStats();

        $expected = [];
        $expected[] = [
            1,
            'select*from yoda_event;',
            false,
            []
        ];
        $expected[] = [
            1,
            'select*from yoda_event where location={};',
            false,
            []
        ];

        self::assertEquals($expected, $patternStats);
    }
}
