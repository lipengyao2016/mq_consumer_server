<?php declare(strict_types=1);


namespace App\Http\Controller;

use App\Model\Logic\MqConsumerLogic;
use Swoft\Http\Message\Request;
use Swoft\Http\Server\Annotation\Mapping\Controller;
use Swoft\Http\Server\Annotation\Mapping\RequestMapping;
use Swoft\Log\Helper\CLog;
use Swoft\Log\Helper\Log;
use Swoft\Task\Exception\TaskException;
use Swoft\Task\Task;
use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Bean\Annotation\Mapping\Inject;

/**
 * Class MqTaskController
 *
 * @since 2.0
 *
 * @Controller(prefix="mqtask")
 */
class MqTaskController
{
    /**  @Inject()
     * @var MqConsumerLogic
     */
    protected $mqConsumerLogic;

    /**
     * @RequestMapping()
     *
     * @return array
     * @throws TaskException
     */
    public function startConsumeMq(): array
    {
        //$data = $this->mqConsumerLogic->startConsume();
     //   return ['taskIds' => $data];
        return [ 'ret' => 'unimplemented'];
    }

    /**
     * @RequestMapping()
     *
     * @return array
     * @throws TaskException
     */
    public function createMqTask(Request $request): array
    {
        $inputData = $request->input();
        CLog::info(__METHOD__.'   $inputData:'.json_encode($inputData));
        $ret = $this->mqConsumerLogic->saveConsumerTask($inputData);
        return ['ret' => $ret];
    }


}