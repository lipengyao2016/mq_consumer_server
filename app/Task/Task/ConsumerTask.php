<?php declare(strict_types=1);


namespace App\Task\Task;

use App\Model\Logic\KafkaConsumerLogic;
use Swoft\Log\Helper\CLog;
use Swoft\Log\Helper\Log;
use Swoft\Task\Annotation\Mapping\Task;
use Swoft\Task\Annotation\Mapping\TaskMapping;
use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Bean\Annotation\Mapping\Inject;

/**
 * Class ConsumerTask
 *
 * @since 2.0
 *
 * @Task(name="consumerTask")
 */
class ConsumerTask
{

    /**
     * @Inject()
     * @var KafkaConsumerLogic
     */
    private $kafkaConsumerLogic;


    /**
     * @TaskMapping(name="consume")
     * @param array $data
     * @return array
     * @throws \Swoft\Apollo\Exception\ApolloException
     * @throws \Swoft\Exception\SwoftException
     */
    public function consumeMq(array $data): array
    {
        CLog::info(__METHOD__.' data:'.json_encode($data).' current pid:'.posix_getpid().
        ' extData:'.json_encode(context()->getRequest()->getExt()));

        $this->kafkaConsumerLogic->consumer($data);
        return ['ret'    => 'ok'];
    }
}