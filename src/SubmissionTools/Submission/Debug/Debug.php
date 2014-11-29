<?php

namespace paslandau\WebAutomator\SubmissionTools\Submission\Debug;


use DateTime;
use paslandau\DataFiltering\Traits\LoggerTrait;
use paslandau\DataFiltering\Util\StringUtil;
use paslandau\IOUtility\IOUtil;
use paslandau\WebAutomator\SubmissionTools\Submission\Steps\SubmissionStepQueue;
use paslandau\WebAutomator\SubmissionTools\Submission\Steps\SubmissionStepResult;

/**
 * @property mixed debugFolder
 */
class Debug
{
    use LoggerTrait;

    const LEVEL_NONE = 0;
    const LEVEL_ERROR = 1;
    const LEVEL_ALL = 2;

    /**
     * @var DebugContainer
     */
    private $container;
    /**
     * @var string
     */
    private $path;
    /**
     * @var null|string
     */
    private $level;

    /**
     * @param DebugContainer $container
     * @param string $path
     * @param string $level
     */
    function __construct(DebugContainer $container, $path, $level = null)
    {
        $this->container = $container;
        if ($level === null) {
            $level = self::LEVEL_NONE;
        }
        $this->level = $level;
        $this->path = $path;
        IOUtil::createDirectoryIfNotExists($path);
    }

    /**
     * @return DebugContainer
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * @return null|string
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    public function addDebugEvent($event, $key)
    {
        $children = $this->container->getChildren();
        $children[$key]->addEvent($event);
    }

    public function activateDebug($submissionQueueList)
    {
        $this->container->clear();
        foreach ($submissionQueueList as $idx => $queue) {
            $this->container->setChild($idx, $this->container->getContainerFor($queue, ""));
        }
    }

    public function saveDebug($queues)
    {
        $date = (new DateTime)->format("Y-m-d-H-i-s");
        $debugSessionFolder = $this->path . "/" . $date;
        IOUtil::createDirectoryIfNotExists($debugSessionFolder);
        /** @var DebugContainer $container */
        $children = $this->container->getChildren();
        foreach ($children as $queueIndex => $container) {
            $save = StringUtil::MakeStringUrlSave($queueIndex);
            $path = IOUtil::combinePaths($debugSessionFolder, "$save.html");
            /** @var SubmissionStepQueue $m */
            $m = $queues[$queueIndex];
            $res = "";
            if (($e = $m->getException()) !== null) {
                if ($this->level < self::LEVEL_ERROR) {
                    continue;
                }
                $messages = [];
                do {
                    $messages[] = htmlentities($e->getMessage());
                    $e = $e->getPrevious();
                } while ($e !== null);
                $errors = "<ul><li>" . implode("</li><li>", $messages) . "</li></ul>";
                $res = '<h2>Submission failed!</h2><div style="background:#ffcccc">' . $errors . '</div>';
            } else {
                if ($this->level < self::LEVEL_ALL) {
                    continue;
                }
//                ob_start();
//                var_dump($m->getResult());
//                $a = ob_get_contents();
//                ob_end_clean();
                $r = $m->getResult();
                $str = [];
                /**
                 * @var string $stepIndex
                 * @var SubmissionStepResult $result
                 */
                foreach ($r as $stepIndex => $result) {
                    $str[] = "<h3>$stepIndex</h3>";
                    $str[] = "<div>" . htmlspecialchars($result->__toString(), ENT_QUOTES) . "</div>";
                }

                $data = "<pre>" . implode("\n", $str) . "</pre>";
                $res = '<h2>Submission successful!</h2><div style="background:#ccffcc">' . $data . '</div>';
            }
            $res .= '<br style="clear:both;">';
            $c = $container->getAsHtml($res);
            IOUtil::writeFileContent($path, $c);
            $this->getLogger()->info("Exported debug info for queue $queueIndex to file://$path");
        }
    }
}