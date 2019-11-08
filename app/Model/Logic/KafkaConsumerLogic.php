<?php declare(strict_types=1);


namespace App\Model\Logic;

use App\Common\http\HttpUtils;
use App\Common\kafka\HighConsumer;
use App\Common\kafka\LowerConsumer;
use Co\Http\Client\Exception;
use Swoft\Apollo\Config;
use Swoft\Apollo\Exception\ApolloException;
use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Bean\Annotation\Mapping\Inject;
use Swoft\Log\Helper\CLog;
use Swoft\Log\Helper\Log;

/**
 * Class ApolloLogic
 *
 * @since 2.0
 *
 * @Bean()
 */
class KafkaConsumerLogic
{
    protected $kafkaServerHost;

    protected $topicName;


    /**
     * ApolloLogic constructor.
     */
    public function __construct()
    {
        $this->kafkaServerHost = '47.107.246.243';
        $this->topicName = 'test_lpy';
    }


    /**
     * @throws ApolloException
     */
    public function consumer($data)
    {
       /* $rk = new \RdKafka\Consumer();
        print_r($rk);
        $rk->addBrokers($this->kafkaServerHost);

        $topic = $rk->newTopic($this->topicName);
        print_r($topic);

        $topic->consumeStart(0, RD_KAFKA_OFFSET_BEGINNING);
        CLog::info(' consumer start msg');
        while (true) {
            $msg = $topic->consume(0, 12000);
            if (null === $msg) {
                CLog::info(' recv null msg  pid:'.posix_getpid());
                continue;
            }
             elseif ($msg->err) {
                 CLog::info(' recv error msg  pid:'.$msg->errstr());
            }
            else {
                print_r($msg);
                CLog::info(' recv unit pid:'. posix_getpid().' msg:'. $msg->payload);
            }
        }*/

        $offset = 0;
        $consumer = new HighConsumer(['ip'=>$this->kafkaServerHost]);
        //$consumer = new LowerConsumer(['ip'=>$this->kafkaServerHost]);
        print_r(' consumer begin. topic:'.$data['topic_name'].
        ' consumerGroupName:'.$data['consumer_group_name']);
        $curCtx = $this;

        try{
            $consumer->setConsumerGroup($data['consumer_group_name'])
                ->setBrokerServer($this->kafkaServerHost)
                ->setConsumerTopic()
               // ->setTopic($this->topicName, 0, $offset)
                ->subscribe([$data['topic_name']])
                ->consumer(function($msg) use ($curCtx,$data,$consumer){
                  //  CLog::info(' recv unit pid:'. posix_getpid().' msg:'.json_encode($msg));
                    try
                    {
                        $curCtx->handleMqMsg($data,$msg);
                        $consumer->commit($msg);
                    }
                    catch (\Exception $e)
                    {
                        CLog::error(__LINE__.' error:'.$e->getTraceAsString());
                        print_r(' handle error not commit offset:'.$msg->offset);
                    }

                });
        }
        catch (\Exception $e)
        {
            print_r($e->getTraceAsString());
        }
        print_r(' consumer exit.topic'.$data['topic_name'].
        ' consumerGroupName:'.$data['consumer_group_name']);
    }

    public function handleMqMsg($mqConfigItem,$msg)
    {
        $handleUrl = $mqConfigItem['handle_url'];
        $method = $mqConfigItem['is_post'];
        $bRet = HttpUtils::request($handleUrl,json_decode($msg->payload,true),($method == 1),false);
        CLog::info(__METHOD__.' url:'.$handleUrl.' method:'.$method.' data:'.$msg->payload
            .' bRet:'.$bRet);
        return $bRet;
    }


}