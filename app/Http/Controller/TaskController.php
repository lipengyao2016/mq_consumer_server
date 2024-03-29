<?php declare(strict_types=1);


namespace App\Http\Controller;

use App\Model\Logic\MqConsumerLogic;
use Swoft\Http\Server\Annotation\Mapping\Controller;
use Swoft\Http\Server\Annotation\Mapping\RequestMapping;
use Swoft\Log\Helper\Log;
use Swoft\Task\Exception\TaskException;
use Swoft\Task\Task;

/**
 * Class TaskController
 *
 * @since 2.0
 *
 * @Controller(prefix="task")
 */
class TaskController
{

    /**
     * @RequestMapping()
     *
     * @return array
     * @throws TaskException
     */
    public function getListByCo(): array
    {
        $extData = ['merchantId' => 3];
        $data = Task::co('testTask', 'list', [12],3,$extData);
        Log::debug(__METHOD__.' data:'.json_encode($data));
        return $data;
    }

    /**
     * @RequestMapping(route="deleteByCo")
     *
     * @return array
     * @throws TaskException
     */
    public function deleteByCo(): array
    {
        $data = Task::co('testTask', 'delete', [12]);
        Log::debug(__METHOD__.' data:'.json_encode($data));
        if (is_bool($data)) {
            return ['bool'];
        }

        return ['notBool'];
    }

    /**
     * @RequestMapping()
     *
     * @return array
     * @throws TaskException
     */
    public function getListByAsync(): array
    {
        $data = Task::async('testTask', 'list', [12]);
        Log::debug(__METHOD__.' data:'.json_encode($data));
        return [$data];
    }

    /**
     * @RequestMapping(route="deleteByAsync")
     *
     * @return array
     * @throws TaskException
     */
    public function deleteByAsync(): array
    {
        $data = Task::async('testTask', 'delete', [12]);
        Log::debug(__METHOD__.' data:'.json_encode($data));
        return [$data];
    }

    /**
     * @RequestMapping()
     *
     * @return array
     * @throws TaskException
     */
    public function returnNull(): array
    {
        $result = Task::co('testTask', 'returnNull', ['name']);
        return [$result];
    }

    /**
     * @RequestMapping()
     *
     * @return array
     * @throws TaskException
     */
    public function returnVoid(): array
    {
        $result = Task::co('testTask', 'returnVoid', ['name']);
        return [$result];
    }

    /**
     * @RequestMapping()
     *
     * @return array
     * @throws TaskException
     */
    public function syncTask(): array
    {
        $result  = Task::co('sync', 'test', ['name']);
        Log::info(__METHOD__.' task 1 end!!');
        $result2 = Task::co('sync', 'testBool', []);
        Log::info(__METHOD__.' task 2 end!!');
        $result3 = Task::co('sync', 'testNull', []);

        Log::info(__METHOD__.'task 3  end!!');
        $data[] = $result;
        $data[] = $result2;
        $data[] = $result3;
        return $data;
    }
}