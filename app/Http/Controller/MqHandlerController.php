<?php declare(strict_types=1);


namespace App\Http\Controller;

use App\Model\Logic\ApolloLogic;
use App\Model\Logic\KafkaProducterLogic;
use App\Model\Logic\RequestBean;
use App\Model\Logic\RequestBeanTwo;
use App\service\ISmsInterface;
use ReflectionException;
use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Bean\Annotation\Mapping\Inject;
use Swoft\Bean\BeanFactory;
use Swoft\Bean\Exception\ContainerException;
use Swoft\Co;
use Swoft\Http\Message\Request;
use Swoft\Http\Server\Annotation\Mapping\Controller;
use Swoft\Http\Server\Annotation\Mapping\RequestMapping;
use Swoft\Log\Helper\CLog;
use Swoft\Log\Helper\Log;

/**
 * Class BeanController
 *
 * @since 2.0
 *
 * @Controller(prefix="mq-handler")
 */
class MqHandlerController
{
    /**
     * @RequestMapping()
     *
     * @return array
     * @throws ReflectionException
     * @throws ContainerException
     * @throws \Swoft\Exception\SwoftException
     */
    public function handleTest1Mq(Request $request): array
    {
        $id = (string)Co::tid();
        $context = context();
        CLog::info(__METHOD__.' TID:'.$id.' cur process id:'.posix_getpid().
        ' data:'.json_encode($request->input()));
        return ['ret' => 'test1mq handle ok'];
    }

    /**
     * @RequestMapping()
     *
     * @return array
     * @throws ReflectionException
     * @throws ContainerException
     * @throws \Swoft\Exception\SwoftException
     */
    public function handleTest2Mq(Request $request): array
    {
        $id = (string)Co::tid();
        $context = context();
        CLog::info(__METHOD__.' TID:'.$id.' cur process id:'.posix_getpid().
            ' data:'.json_encode($request->input()));
        return ['ret' => 'test2mq handle ok'];
    }

}