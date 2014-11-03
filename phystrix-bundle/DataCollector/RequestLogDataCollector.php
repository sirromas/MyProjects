<?php
namespace Odesk\Bundle\PhystrixBundle\DataCollector;

use Odesk\Phystrix\AbstractCommand;
use Odesk\Phystrix\RequestLog;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;

/**
 * Collects data from Phystrix RequestLog. Makes it compatible to use with Symfony profiler and WebProfiler.
 */
class RequestLogDataCollector extends DataCollector
{
    private $requestLog;

    public function __construct(RequestLog $requestLog)
    {
        $this->requestLog = $requestLog;
    }

    /**
     * {@inheritdoc}
     */
    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
        $this->data = array('commands' => array());

        /** @var AbstractCommand $command */
        foreach ($this->requestLog->getExecutedCommands() as $command) {
            $time = $command->getExecutionTimeInMilliseconds();
            if (!$time) {
                $time = 0;
            }
            $this->data['commands'][] = array(
                'class' => get_class($command),
                'duration' => $time,
                'events' => $command->getExecutionEvents(),
            );
        }
    }

    public function getCommands()
    {
        return $this->data['commands'];
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'phystrix';
    }
}
