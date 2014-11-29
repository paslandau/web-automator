<?php
namespace paslandau\WebAutomator\SubmissionTools\DataFilteringAdapter;

use paslandau\DataFiltering\Transformation\DataTransformerInterface;
use paslandau\DataFiltering\Extraction\AbstractBaseExtractor;
use paslandau\DataFiltering\Extraction\DataExtractorInterface;
use paslandau\WebAutomator\SubmissionTools\Transaction\ResponseData\ResponseDataInterface;
use paslandau\DataFiltering\Util\ReflectionUtil;

class ResponseDataExtractor extends AbstractBaseExtractor implements DataExtractorInterface
{

    /**
     * @var string
     */
    private $method;

    /**
     * @param string $method
     * @param DataTransformerInterface $transformer [optional]. Default: null.
     * @throws \InvalidArgumentException
     */
    function __construct($method, DataTransformerInterface $transformer = null)
    {
        $methods = ReflectionUtil::GetMethods(ResponseDataInterface::class, \ReflectionMethod::IS_PUBLIC);
        if (!array_key_exists($method, $methods)) {
            throw new \InvalidArgumentException("Method '$method' is invalid. Possible values: " . implode(", ", array_keys($methods)));
        }
        $this->method = $method;
        parent::__construct($transformer);
    }

    /**
     * @return string
     */
    public function getMethod(){
        return $this->method;
    }

    /**
     * Extracts the information of $responseData and uses $this->transformer on it (if set).
     * @param ResponseDataInterface $responseData
     * @return mixed
     */
    protected function extract(ResponseDataInterface $responseData)
    {
//        $extracted = $this->method->invoke($responseData); << won't work on Mocks
        $extracted = $responseData->{$this->method}();
        return $extracted;
    }

}