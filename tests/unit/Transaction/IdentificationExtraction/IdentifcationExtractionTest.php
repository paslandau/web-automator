<?php

use paslandau\DataFiltering\Extraction\DataExtractorInterface;
use paslandau\DataFiltering\Identification\IdentificationInterface;
use paslandau\WebAutomator\SubmissionTools\Transaction\IdentificationExtraction\IdentificationExtractor;
use paslandau\WebAutomator\SubmissionTools\Transaction\ResponseData\ResponseDataInterface;

class IdentifcationExtractionTest extends PHPUnit_Framework_TestCase
{
    public function test_extract()
    {
        $identifyRes = true;
        $extractRes = "extract";

        $mock = $this->getMock(IdentificationInterface::class);
        $mock->expects($this->once())->method("isIdentifiedBy")->will($this->returnValue($identifyRes));
        /** @var IdentificationInterface $mock */

        $exMock = $this->getMock(DataExtractorInterface::class);
        $exMock->expects($this->once())->method("getData")->will($this->returnValue($extractRes));
        /** @var DataExtractorInterface $exMock */

        $e = new IdentificationExtractor($mock, $exMock);

        /** @var ResponseDataInterface $resp */
        $resp = $this->getMock(ResponseDataInterface::class);

        $actual = $e->identify($resp);
        $this->assertEquals($identifyRes,$actual,"Identification result does not match");

        $actual = $e->extract($resp);
        $this->assertEquals($extractRes,$actual,"Extraction result does not match");
    }
}
