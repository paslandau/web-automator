<?php

namespace paslandau\WebAutomator\SubmissionTools\Submission\Debug;


use GuzzleHttp\Event\AbstractTransferEvent;
use GuzzleHttp\Event\EndEvent;
use GuzzleHttp\Event\ErrorEvent;
use paslandau\ArrayUtility\ArrayUtil;
use paslandau\BooleanExpressions\ExpressionInterface;
use paslandau\DomUtility\DomUtil;
use paslandau\ExceptionUtility\ExceptionUtil;
use paslandau\DataFiltering\Events\DataEmitterInterface;
use paslandau\DataFiltering\Events\DataProcessedEvent;
use paslandau\DataFiltering\Extraction\DataExtractorInterface;
use paslandau\DataFiltering\Transformation\Cache;
use paslandau\DataFiltering\Transformation\DataTransformerInterface;
use paslandau\DataFiltering\Identification\IdentificationInterface;
use paslandau\WebAutomator\SubmissionTools\Transaction\IdentificationExtraction\IdentificationExtractionInterface;
use paslandau\WebAutomator\SubmissionTools\Submission\Recovery\RecoveryStrategyInterface;
use paslandau\WebAutomator\SubmissionTools\Submission\Steps\SubmissionStepInterface;
use paslandau\WebAutomator\SubmissionTools\Submission\Steps\SubmissionStepQueueInterface;
use paslandau\DataFiltering\Traits\LoggerTrait;
use paslandau\WebAutomator\SubmissionTools\Transaction\RequestInfo\RequestInfoBuilder;
use paslandau\DataFiltering\Util\StringUtil;

class DebugContainer
{
    use LoggerTrait;

    private $label;
    private $object;
    private $children = array();
    private $events = array();
    /**
     * @var Cache
     */
    private $cache;

    function __construct($object, $name, Cache $cache = null, $savePath = "", $children = array(), $events = array())
    {
        $this->object = $object;
        $this->label = $name;
        $this->children = $children;
        $this->events = $events;
        if ($cache === null) {
            $cache = new Cache();
        }
        $this->cache = $cache;
    }

    public function clear()
    {
        $this->children = array();
        $this->events = array();
        $this->cache->clear();
    }

    public function getContainerFor($obj = null, $name = null)
    {
        if ($obj === null) {
            return null;
        }
        if ($name === null) {
            $name = "";
        }
        $debug = null;
        if ($obj instanceof SubmissionStepQueueInterface) {
            $debug = $this->getContainerForObject($obj, $name);
        } elseif ($obj instanceof SubmissionStepInterface) {
            $debug = $this->getContainerForObject($obj, $name);
        } elseif ($obj instanceof IdentificationInterface) {
            $debug = $this->getContainerForObject($obj, $name);
        } elseif ($obj instanceof ExpressionInterface) {
            $debug = $this->getContainerForObject($obj, $name);
        } elseif ($obj instanceof RequestInfoBuilder) {
            $debug = $this->getContainerForObject($obj, $name);
        } elseif ($obj instanceof DataExtractorInterface) {
            $debug = $this->getContainerForObject($obj, $name);
        } elseif ($obj instanceof DataTransformerInterface) {
            $debug = $this->getContainerForObject($obj, $name);
        } elseif ($obj instanceof IdentificationExtractionInterface) {
            $debug = $this->getContainerForObject($obj, $name);
        } elseif ($obj instanceof RecoveryStrategyInterface) {
            $debug = $this->getContainerForObject($obj, $name);
        }
        if ($obj instanceof DataEmitterInterface) {
            $this->setEventFor($obj, $debug);
        }
        return $debug;
    }

    public function getContainerForObject($obj, $name)
    {
//        if(StringUtil::contains(get_class($obj),"RequestInfoBuilder")){
//            echo "$name, ".get_class($obj)."\n";
//        }
        $debug = new DebugContainer($obj, $name, $this->cache);
        $c = new \ReflectionClass(get_class($obj));
        $properties = $c->getProperties();
        foreach ($properties as $property) {
            $property->setAccessible(true);
            $prop = $property->getName();
            $val = $property->getValue($obj);
            if ($val instanceof DebugContainer) { // stop endless recursion
                continue;
            }
            if (is_array($val)) { // display array directly without additional level (like And_) since arrays don't provide good context information
                foreach ($val as $key => $item) {
                    $propName = $prop . "_" . $key;
                    $propDebug = $this->getContainerFor($item, $propName);
                    if ($propDebug !== null) {
                        $debug->children[$propName] = $propDebug;
                    }
                }
            } else {
                $propDebug = $this->getContainerFor($val, $prop);
                if ($propDebug !== null) {
                    $debug->children[$prop] = $propDebug;
                }
            }
        }
        if ($obj instanceof \Traversable) { // e.g. And_, Or_, etc.
            foreach ($obj as $key => $item) {
                $propName = $key;
                $propDebug = $this->getContainerFor($item, $propName);
                if ($propDebug !== null) {
                    $debug->children[$propName] = $propDebug;
                }
            }
        }
        return $debug;
    }

    public function setEventFor(DataEmitterInterface $emitter, DebugContainer $container)
    {
        $debugCallback = function (DataProcessedEvent $event) use ($container, $emitter, &$debugCallback) {
//            if (!$innerEr->getIsGettingData()) {
//                return;
//            }
            $container->events[] = $event; // add event to container
            $event->stopPropagation(); // stop other events from being executed
            $emitter->detachOnProcessed($debugCallback); // detach from emitter (>> execute this listener only once)
        };
        $emitter->attachOnProcessed($debugCallback);
    }

    public function getAsHtml($result)
    {
        $dataTree = $this->getDataTree();
        $cache = $this->cache->getCache();
        foreach ($cache as $id => $data) {
            if (json_encode($cache) === false) {
                $msg = "Failed converting 'cache' to json: " . json_last_error_msg();
                $cache[$id] = $msg;
                $this->getLogger()->addError($msg);
                continue;
            }
            $cache[$id] = $data["data"];
        }

        if (($jsonCache = json_encode($cache)) === false) {
            $this->getLogger()->addError("Failed converting 'cache' to json: " . json_last_error_msg());
        }
        if (($jsonTree = json_encode([$dataTree["tree"]])) === false) {
            $this->getLogger()->addError("Failed converting 'tree' to json: " . json_last_error_msg());
        }
        if (($jsonData = json_encode($dataTree["data"])) === false) {
            $this->getLogger()->addError("Failed converting 'data' to json: " . json_last_error_msg());
        }
        $c = '<!DOCTYPE html>
        <html><head><meta charset="utf-8"><title></title>
        <script src="http://ajax.googleapis.com/ajax/libs/jquery/2.0.0/jquery.min.js"></script>
        <script src="http://js.myseosolution.de/jqtree/tree.jquery.js"></script>
        <link rel="stylesheet" href="http://js.myseosolution.de/jqtree/jqtree.css">
        </head><body>
        ' . $result . '
        <div id="tree1" style="width:30%; float:left; border:solid 1px black;"></div>
        <iframe id="data" src="about:blank" style="width:68%; height:500px; float:right; border:solid 1px black;">Data</iframe>
        <iframe id="dataPre" src="about:blank" style="width:68%; height:500px; float:right; border:solid 1px black;">Data</iframe>
        <!-- <div id="data" style="width:68%; float:right; border:solid 1px black;">Data</div> -->
        <script>
        var cache = ' . $jsonCache . ';
        var tree = ' . $jsonTree . ';
        var data = ' . $jsonData . ';

        function endsWith(str, suffix) {
            return str.indexOf(suffix, str.length - suffix.length) !== -1;
        }

        $(function() {
            var $tree = $(\'#tree1\');

            $tree.tree({
                data: tree,
                autoOpen: 0,
                useContextMenu: false,
                onCreateLi: function(node, $li) {
                    var nodeId = node.id+"";
                    if(nodeId.indexOf("_events_") > -1){
                        $li.find(\'.jqtree-element\').append(
                            \' <a href="#node-\'+ node.id +\'_before" class="edit" data-node-id="\'+
                            node.id +\'_before">dataBefore</a> <a href="#node-\'+ node.id +\'_after" class="edit" data-node-id="\'+
                            node.id +\'_after">dataAfter</a>\'
                        );
                    }else if(nodeId.indexOf("_events") == -1){
                        $li.find(\'.jqtree-element\').append(
                            \' <a href="#node-\'+ node.id +\'_object" class="edit" data-node-id="\'+
                            node.id +\'_object">object</a>\'
                        );
                    }
                }
            });

            // Handle a click on the edit link
            $tree.on(
                \'click\', \'.edit\',
                function(e) {
                    // Get the id from the \'node-id\' data property
                    var node_id = $(e.target).data(\'node-id\');

                    if(node_id.indexOf("_events_") > -1){
                        var parts = node_id.split("_events_");
                        var data_id = parts[0];
                        var event_parts = parts[1].split("_");
                        var event_id = event_parts[0];
                        var event_data = event_parts[1];

                        // Get the node from the tree
                        //var node = $tree.tree(\'getNodeById\', node_id);
                        var dataToShow = data[data_id]["events"][event_id][event_data];
                    }

                    if(node_id.indexOf("_object") > -1){
                        var parts = node_id.split("_object");
                        var data_id = parts[0];
                        var dataToShow = data[data_id]["object"];
                    }

                    if (dataToShow) {
                        dataToShow = cache[dataToShow];
                        var doc = document.getElementById("data").contentWindow.document;
                        doc.open();
                        doc.write(dataToShow);
                        doc.close();
                        var c = $("<div/>").text(dataToShow).html();
                        doc = document.getElementById("dataPre").contentWindow.document;
                        doc.open();
                        doc.write("<pre>"+ c +"</pre>");
                        doc.close();
//                        $("#data").html(dataToShow);
                    }
                }
            );
        });
        </script>
        </body>
        </html>
        ';
        return $c;
    }

    public function getDataTree($id = 0)
    {
        $data = array();
        $tree = array();
        $data[$id] = array();
        $data[$id]["object"] = $this->getDataReference($this->object);
        $data[$id]["events"] = array();
        foreach ($this->events as $event) {
            $data[$id]["events"][] = $this->event2StringArray($event);
        }
        $tree["label"] = (new \ReflectionClass($this->object))->getShortName() . ":" . $this->label;
        $tree["id"] = $id;
        $tree["children"] = array();

        if (count($this->events) > 0) {
            $events = [];
            $event_id = $id . "_events";
            $i = 0;
            foreach ($this->events as $event) {
                $inner_event_id = $event_id . "_$i";
                $events[] = [
                    'label' => "event_$i",
                    'id' => $inner_event_id,
                ];
                $i++;
            }
            $tree["children"][] = [
                'label' => "_events",
                'id' => $event_id,
                'children' => $events,
            ];
        }
        $i = 0;
        foreach ($this->children as $child) {
            /** @var DebugContainer $child */
            if ($child === null) {
                continue;
            }
            $id .= "_$i";
            $dataTree = $child->getDataTree($id);
            $tree["children"][] = $dataTree["tree"];
            $data = array_merge($data, $dataTree["data"]);
            $i++;
        }
        $res = array("tree" => $tree, "data" => $data);
        return $res;
    }

    public function setChild($index, DebugContainer $container)
    {
        $this->children[$index] = $container;
    }

    public function addEvent($event)
    {
        $this->events[] = $event;
    }

    /**
     * @return DebugContainer[]
     */
    public function getChildren()
    {
        return $this->children;
    }

    private function getDataReference($data)
    {
        $data = $this->getString($data);
        if ($this->cache->contains($data)) {
            return $this->cache->getId($data);
        } else {
            return $this->cache->addToCache($data, $data, null);
        }
    }

    private function getString($data)
    {
        if ($data instanceof \DOMDocument) {
            $data = $data->saveHTML();
        } elseif ($data instanceof \DOMNode || $data instanceof \DOMNodeList) {
            $data = DomUtil::toString($data);
        } elseif (StringUtil::CanBeString($data)) {
            $data = StringUtil::strval($data);
        } elseif (is_array($data)) {
            $vals = [];
            foreach ($data as $key => $d) {
                if (StringUtil::CanBeString($d)) {
                    $vals[] = "$key - " . StringUtil::strval($d);
                } else {
                    $vals[] = "$key - [cannot be shown as string]";
                }
            }
            $data = implode("\n", $vals);
        } else {
//            ob_start();
//            @var_dump($data);
//            $data = ob_get_contents();
//            ob_end_clean();
            $data = "[cannot be shown as string]";
        }
        $data = (string)$data;
        return $data;
    }

    private function event2StringArray($event)
    {
        $array = [];
        $datas = [];
        if ($event instanceof DataProcessedEvent) {

            $array["emitter"] = $this->getDataReference($event->getEmitter());

            $datas = [
                "before" => $event->getDataBefore(),
                "after" => $event->getDataAfter()
            ];
        }
        if ($event instanceof AbstractTransferEvent) {
            $array["emitter"] = $this->getDataReference($event->getClient());
            $response = $event->getResponse();
            $error = null;
            if ($event instanceof ErrorEvent || $event instanceof EndEvent) {
                $error = $event->getException();
            }
            if ($response !== null) {
                $statusCode = $response->getStatusCode();
                $url = $response->getEffectiveUrl();
                $body = $response->getBody();
                $headers = ArrayUtil::toString($response->getHeaders());
                $eString = "Error: " . ExceptionUtil::getAllErrorMessagesAsString($error) . "\n\n";
                $response = "{$eString}StatusCode:$statusCode\n\nURL: $url\n\nHeaders: $headers\n\nBody:\n$body";
            } elseif ($error !== null) {
                $response = "Error: " . ExceptionUtil::getAllErrorMessagesAsString($error);
            }
            $datas = [
                "before" => $event->getRequest(),
                "after" => $response
            ];
        }
        //save resource
        foreach ($datas as $dataName => $data) {
            $data = $this->getDataReference($data);
            $array[$dataName] = $data;
        }
        return $array;
    }

}