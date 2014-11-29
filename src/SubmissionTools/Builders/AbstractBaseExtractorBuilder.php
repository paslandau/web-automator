<?php

namespace paslandau\WebAutomator\SubmissionTools\Builders;


use paslandau\DataFiltering\Extraction\DataExtractorInterface;
use paslandau\DataFiltering\Transformation\BooleanTransformerInterface;
use paslandau\DataFiltering\Transformation\DataTransformerInterface;
use paslandau\DataFiltering\Transformation\DomDocumentTransformerInterface;
use paslandau\DataFiltering\Transformation\StringTransformerInterface;

abstract class AbstractBaseExtractorBuilder
{
    /**
     * @var DataExtractorInterface
     */
    protected $base;

    /**
     * @var DataTransformerInterface
     */
    protected $trans;

    public function postProcessToBoolean(BooleanTransformerInterface $trans)
    {
        return $this->postProcess($trans);
    }

    public function postProcessToString(StringTransformerInterface $trans)
    {
        return $this->postProcess($trans);
    }

    public function postProcessToDomDocument(DomDocumentTransformerInterface $trans)
    {
        return $this->postProcess($trans);
    }

    public function postProcess(DataTransformerInterface $trans)
    {
        $successor = $trans;
        $seen = new \SplObjectStorage();
        $seen->attach($successor);
        while ($successor->getPredecessor() !== null) {
            $successor = $successor->getPredecessor();
            if ($seen->contains($successor)) {
                $chain = [];
                $i = 0;
                $recursionAt = $i;
                foreach ($seen as $obj) {
                    $here = "";
                    if ($obj === $successor) {
                        $here = "[!!!]";
                        $recursionAt = $i;
                    }
                    $chain[$i] = md5(spl_object_hash($obj)) . " $here " . (new \ReflectionClass($obj))->getShortName();
                    $i++;
                }
                $chain[] = $chain[$recursionAt];
                $chain = array_reverse($chain);
                throw new \RuntimeException("Infinite recursion detetcted:\n > " . implode("\n > ", $chain));
            }
            $seen->attach($successor);
        }
        $successor->setPredecessor($this->trans);
        $this->trans = $trans;
        return $this;
    }

    /**
     * @return DataExtractorInterface
     */
    public function build()
    {
        $this->base->setTransformer($this->trans);
        return $this->base;
    }
} 