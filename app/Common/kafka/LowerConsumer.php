<?php
/**
 * Created by PhpStorm.
 * User: qkl
 * Date: 2018/8/14
 * Time: 15:45
 */

namespace App\Common\kafka;
use Swoft\Log\Helper\CLog;

class LowerConsumer
{
    private $consumer;
    private $consumerTopic;
    private $topicNames;
    private $groupName;
    private $brokers;
    private $rk;

    public function __construct($config = [])
    {
        $this->rk = new KafkaConfig($config);
        $this->rkConf = $this->rk->getConf();
        $this->config = $this->rk->getConfig();
        $this->brokerConfig = $this->rk->getBrokerConfig();

        $this->topicNames = '';
        $this->groupName = '';
        $this->brokers = '';

    }

    /**
     * 设置消费组
     * @param $groupName
     */
    public function setConsumerGroup($groupName)
    {
      /*  */
        $this->groupName = $groupName;
        print_r(' setConsumerGroup ok.');
        return $this;
    }

    /**
     * 设置服务broker
     * $broker: 127.0.0.1|127.0.0.1:9092|127.0.0.1:9092,127.0.0.1:9093
     * @param $groupName
     */
    public function setBrokerServer($broker)
    {
       // $this->rkConf->set('metadata.broker.list', $broker);\
        $this->brokers = $broker;
        print_r(' setBrokerServer ok.');
        return $this;
    }

    /**
     * 设置服务broker
     * $broker: 127.0.0.1|127.0.0.1:9092|127.0.0.1:9092,127.0.0.1:9093
     * @param $groupName
     */
    public function setTopic($topicName, $partition = 0, $offset = 0)
    {
        //$this->rk->setTopic($topicName, $partition, $offset);
        print_r(__METHOD__.'  ok.');
        return $this;
    }

    public function setConsumerTopic()
    {
        $this->topicConf = new \RdKafka\TopicConf();
        print_r(' setConsumerTopic 1.');
        $this->topicConf->set('auto.offset.reset', 'smallest');
        //$this->topicConf->set('auto.commit.enable', true);
        $this->topicConf->set('auto.commit.enable', 1);
        $this->topicConf->set('auto.commit.interval.ms', 1000);
        $this->topicConf->set('offset.store.method', 'broker');
        print_r(' setConsumerTopic ok.');
        return $this;
    }

    public function getConsumerTopic()
    {
        return $this->topicConf;
    }

    public function subscribe($topicNames)
    {
        //print_r($this->consumer);
        $this->rkConf->set('group.id', $this->groupName);
        $this->consumer = new \RdKafka\Consumer($this->rkConf);
        $this->consumer->addBrokers($this->brokers);
        $this->consumerTopic = $this->consumer->newTopic($topicNames[0], $this->topicConf);
        // Start consuming partition 0
        $this->consumerTopic->consumeStart(0, RD_KAFKA_OFFSET_STORED);
        $this->topicNames = $topicNames;
        print_r(' subscribe end.');
        return $this;
    }


    public function consumer(\Closure $callback)
    {
        //参数1表示消费分区，这里是分区0
        //参数2表示同步阻塞多久

        while (true) {
            $message = $this->consumerTopic->consume(0, 120 * 1000);
            if (null === $message) {
               // ' recv null msg  pid:'.posix_getpid() .
                print_r(
                    ' topicNames:'.json_encode($this->topicNames)
                    .' groupNames:'.json_encode($this->groupName));
                continue;
            }
            elseif ($message->err) {
                    print_r(
                        ' topicNames:'.json_encode($this->topicNames)
                        .' groupNames:'.json_encode($this->groupName)
                        .' error:'.$message->errstr());
            }
            else {
                print_r($message);
                print_r(' topicNames:'.json_encode($this->topicNames)
                    .' groupNames:'.json_encode($this->groupName)
                );
                try{
                    $callback($message);
                   // $this->consumerTopic->commit($message);
                   //print_r(' handle ok commit ok. offset:'.$message->offset);
                }
                catch (\Exception $exception)
                {
                    //print_r(' handle error not commit offset:'.$message->offset);
                }

            }
        }

       /* var_dump($message);
        switch ($message->err) {
            case RD_KAFKA_RESP_ERR_NO_ERROR:
                //todo 消费
                $callback($message);
                break;
            case RD_KAFKA_RESP_ERR__PARTITION_EOF:
                print_r("No more messages; will wait for more\n");
                break;
            case RD_KAFKA_RESP_ERR__TIMED_OUT:
                print_r("Timed out\n");
                break;
            default:
                echo $message->err . ":" . $message->errstr;
//                throw new \Exception($message->errstr(), $message->err);
                break;
        }*/

    }
}