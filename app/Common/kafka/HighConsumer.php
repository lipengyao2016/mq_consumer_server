<?php
/**
 * Created by PhpStorm.
 * User: qkl
 * Date: 2018/8/14
 * Time: 15:45
 */

namespace App\Common\kafka;
use Swoft\Log\Helper\CLog;

class HighConsumer
{
    private $consumer;
    private $consumerTopic;
    private $topicNames;
    private $groupName;

    public function __construct($config = [])
    {
        $this->rk = new KafkaConfig($config);
        $this->rkConf = $this->rk->getConf();
        $this->config = $this->rk->getConfig();
        $this->brokerConfig = $this->rk->getBrokerConfig();

        $this->topicNames = '';
        $this->groupName = '';

    }

    /**
     * 设置消费组
     * @param $groupName
     */
    public function setConsumerGroup($groupName)
    {
        $this->rkConf->set('group.id', $groupName);
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
        $this->rkConf->set('metadata.broker.list', $broker);
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
        $this->rk->setTopic($topicName, $partition, $offset);
        print_r(__METHOD__.'  ok.');
        return $this;
    }

    public function setConsumerTopic()
    {
      //  $this->topicConf = new \RdKafka\TopicConf();

       // print_r(' setConsumerTopic 1.');
         $this->rkConf->dump();
         $this->rkConf->set('auto.offset.reset', 'smallest');
         $this->rkConf->set('enable.auto.commit', 0);
        // $this->rkConf->set('offset.store.method', 'broker');

        //$this->topicConf->set('request.required.acks', $this->brokerConfig['request.required.acks']);
        //在interval.ms的时间内自动提交确认、建议不要启动
       /* $this->topicConf->set('auto.commit.enable', $this->brokerConfig['auto.commit.enable']);
        if ($this->brokerConfig['auto.commit.enable']) {
            $this->topicConf->set('auto.commit.interval.ms', $this->brokerConfig['auto.commit.interval.ms']);
        }*/
       // print_r(' setConsumerTopic 2.');
        // 设置offset的存储为file
//        $this->topicConf->set('offset.store.method', 'file');
//        $this->topicConf->set('offset.store.path', __DIR__);
        // 设置offset的存储为broker
       // $this->rkConf->set('offset.store.method', 'broker');
        // $this->topicConf->set('offset.store.method', $this->brokerConfig['offset.store.method']);
       // if ($this->brokerConfig['offset.store.method'] == 'file') {
        //    $this->topicConf->set('offset.store.path', $this->brokerConfig['offset.store.path']);
      //  }
       // print_r(' setConsumerTopic 3.');
        // Set where to start consuming messages when there is no initial offset in
        // offset store or the desired offset is out of range.
        // 'smallest': start from the beginning
       // $this->topicConf->set('auto.offset.reset', 'smallest');
        //$this->topicConf->set('auto.offset.reset', $this->brokerConfig['auto.offset.reset']);
       // print_r(' setConsumerTopic 4.');
        //设置默认话题配置
        //$this->rkConf->setDefaultTopicConf($this->topicConf);

        print_r(' setConsumerTopic ok.');

        return $this;
    }

    public function getConsumerTopic()
    {
        return $this->topicConf;
    }

    public function subscribe($topicNames)
    {
        $this->consumer = new \RdKafka\KafkaConsumer($this->rkConf);
        $this->consumer->subscribe($topicNames);
        $this->topicNames = $topicNames;
        print_r(' subscribe end.');
        return $this;
    }

    public function commit($msg)
    {
        $this->consumer->commit($msg);
        print_r(' handle ok commit ok. offset:'.$msg->offset);
    }

    public function consumer(\Closure $handle)
    {
        print_r(' consumer start.');
        while (true) {
            $message = $this->consumer->consume(120*1000);
            if (null === $message) {
                print_r(' recv null msg  pid:'.posix_getpid()
                    .' topicNames:'.json_encode($this->topicNames)
                    .' groupNames:'.json_encode($this->groupName));
                continue;
            }
            elseif ($message->err) {
            /*    print_r(' recv error msg  pid:'.posix_getpid()
                    .' topicNames:'.json_encode($this->topicNames)
                    .' groupNames:'.json_encode($this->groupName)
                    .' error:'.$message->errstr());*/
            }
            else {
                print_r($message);
                print_r(' recv unit pid:'. posix_getpid()
                    .' topicNames:'.json_encode($this->topicNames)
                    .' groupNames:'.json_encode($this->groupName)
                );
               $handle($message);
            }
          /*  switch ($message->err) {
                case RD_KAFKA_RESP_ERR_NO_ERROR:
                    $handle($message);
                    break;
                case RD_KAFKA_RESP_ERR__PARTITION_EOF:
                    print_r(' pid:'.posix_getpid()."No more messages; will wait for more\n");
                    break;
                case RD_KAFKA_RESP_ERR__TIMED_OUT:
                    CLog::warning(' pid:'.posix_getpid()."Timed out .\n");
                    break;
                default:
                    //throw new \Exception($message->errstr(), $message->err);
                    CLog::error(' pid:'.posix_getpid()." msg error:".$message->errstr().' err:'.$message->err);
                    break;
            }*/
        }
        print_r(' consumer end.');
    }


}