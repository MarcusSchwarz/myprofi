<?php

namespace MyProfiTests;

/**
 * Class HelperTest
 * @package MyProfiTests
 */
class HelperTest extends \PHPUnit_Framework_TestCase
{

    public function testOutputsIndividualText()
    {
        $docFile = __DIR__ . '/../outputs/doc.txt';

        if (!is_readable($docFile)) {
            self::markTestSkipped('file to compare not found');
        }

        $compare = file_get_contents($docFile);

        $mock = $this->getMockBuilder(\MyProfi\Helper::class)
            ->setMethods(['output'])
            ->getMock();

        $mock->expects(self::once())
            ->method('output')
            ->willReturnArgument(0);

        self::assertEquals('individual text' . "\n\n" . $compare, $mock->doc('individual text'));
    }
}
