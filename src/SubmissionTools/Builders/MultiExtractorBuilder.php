<?php
namespace paslandau\WebAutomator\SubmissionTools\Builders;


use paslandau\DataFiltering\Transformation\ArraySelectSingleTransformer;
use paslandau\DataFiltering\Transformation\BaseTransformer;
use paslandau\DataFiltering\Transformation\UrlAbsolutizerTransformer;
use paslandau\DataFiltering\Events\DataProcessedEvent;
use paslandau\DataFiltering\Extraction\DataExtractorInterface;
use paslandau\DataFiltering\Extraction\MultiDataExtractor;
use paslandau\WebAutomator\SubmissionTools\DataFilteringAdapter\ResponseDataExtractor;

class MultiExtractorBuilder extends AbstractBaseExtractorBuilder{

    /**
     * @var DataExtractorInterface[]
     */
    private $extractors;

    private function __construct(array $extractors = null)
    {
        if($extractors === null){
            $extractors = [];
        }
        $this->extractors = $extractors;
    }

    public static function init(array $extractors = null)
    {
        return new self($extractors);
    }

    public function add($key, DataExtractorInterface $extractor)
    {
        $this->extractors[$key] = $extractor;
        return $this;
    }

    public function absoluteUrlFromXpath($xpathToUrl){
        $relativeUrlExtractor = Build::extractor()->useDomDocument()->xpathString($xpathToUrl)->build();
        return $this->absoluteUrl($relativeUrlExtractor);
    }

    public function absoluteUrl(DataExtractorInterface $relativeUrlExtractor){
        $basetrans = new BaseTransformer();
        $baseUrlExtractor = new ResponseDataExtractor("getUrl", $basetrans);
        $relUrlTrans = $relativeUrlExtractor->getTransformer();
        $absoluteUrlT = new UrlAbsolutizerTransformer("", $relUrlTrans,false);
        $relativeUrlExtractor->setTransformer($absoluteUrlT);
        $updateUrlFunc = function (DataProcessedEvent $event) use ($absoluteUrlT) {
            /** @var string $url */
            $url = $event->getDataAfter();
            $absoluteUrlT->setBaseUrl($url);
        };
        $basetrans->attachOnProcessed($updateUrlFunc);
        $extractors = [
            'baseUrl' => $baseUrlExtractor,
            'url' => $relativeUrlExtractor,
        ];
        $this->trans = new ArraySelectSingleTransformer('url'); // will be attached to $this->base on build()
        $this->extractors = $extractors;
        return $this;
    }

    public function build(){
        $this->base = new MultiDataExtractor($this->extractors);
        return parent::build();
    }
} 