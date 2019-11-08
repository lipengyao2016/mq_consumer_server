<?php declare(strict_types=1);


namespace App\Model\Logic;

use App\Model\Dao\MqConsumerDao;
use Swoft\Apollo\Config;
use Swoft\Apollo\Exception\ApolloException;
use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Bean\Annotation\Mapping\Inject;
use Swoft\Log\Helper\CLog;
use Swoft\Log\Helper\Log;
use Swoft\Task\Task;
/**
 * Class MqConsumerLogic
 *
 * @since 2.0
 *
 * @Bean()
 */
class MqConsumerLogic
{
    /**
     * @Inject()
     *
     * @var MqConsumerDao
     */
    private $mqConsumerDao;

    /**
     * ApolloLogic constructor.
     */
    public function __construct()
    {
        $this->configData = null;
    }

    public function saveConsumerTask($data)
    {
        return $this->mqConsumerDao->insertBatch($data);
    }


    /**
     * @throws ApolloException
     */
    public function startConsume()
    {
       $consumerRet = $this->mqConsumerDao->getList([
           'select'=>['id','topic_name','consumer_group_name','handle_url','is_post'],
           'orderby'=>'id desc',
           'page' =>1,
           'limit' => 100]);

     //  CLog::info(__METHOD__.' get delivery list  $consumerList:'.json_encode($consumerRet));

       $taskIds = [];
       foreach ($consumerRet as $consumerItem)
       {
           CLog::info(__METHOD__.' begin delivery task consumerItem:'.json_encode($consumerItem));
           if(empty($consumerItem['handle_url']) || empty($consumerItem['topic_name'])
               || empty($consumerItem['consumer_group_name']) )
                   continue;

           $data = Task::async('consumerTask', 'consume', [$consumerItem]);
           array_push($taskIds,$data);
         //  CLog::info(__METHOD__.' single task Id:'.json_encode($data));
       }

       CLog::info(__METHOD__.' $taskIds:'.count($taskIds));
       return $taskIds;
    }


}