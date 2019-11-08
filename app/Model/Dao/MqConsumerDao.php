<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/11/7
 * Time: 11:42
 */

namespace App\Model\Dao;

use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Bean\Annotation\Mapping\Inject;

/**
 * Class MqConsumerDao
 *
 * @since 2.0
 *
 * @Bean()
 */
class MqConsumerDao extends BaseDao
{
    /**
     * MqConsumerDao constructor.
     */
    public function __construct()
    {
        $this->tableName = 'sj_mq_consumer';
    }

}