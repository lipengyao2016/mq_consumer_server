<?php declare(strict_types=1);
/**
 * This file is part of Swoft.
 *
 * @link     https://swoft.org
 * @document https://swoft.org/docs
 * @contact  group@swoft.org
 * @license  https://github.com/swoft-cloud/swoft/blob/master/LICENSE
 */

namespace App\Common;

use ReflectionException;
use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Bean\Annotation\Mapping\Inject;
use Swoft\Bean\Exception\ContainerException;
use Swoft\Consul\Agent;
use Swoft\Consul\Exception\ClientException;
use Swoft\Consul\Exception\ServerException;
use Swoft\Log\Helper\Log;
use Swoft\Rpc\Client\Client;
use Swoft\Rpc\Client\Contract\ProviderInterface;

/**
 * Class RpcProvider
 *
 * @since 2.0
 *
 * @Bean()
 */
class RpcProvider implements ProviderInterface
{
    /**
     * @Inject()
     *
     * @var Agent
     */
    private $agent;

    /**
     * @param Client $client
     *
     * @return array
     * @throws ReflectionException
     * @throws ContainerException
     * @throws ClientException
     * @throws ServerException
     * @example
     * [
     *     'host:port',
     *     'host:port',
     *     'host:port',
     * ]
     */
    public function getList(Client $client): array
    {
        //Log::debug(__METHOD__.'  start');
        // Get health service from consul
        $services = $this->agent->services();

        $result = $services->getResult();
        var_dump($result);
        Log::debug(__METHOD__.'  services:'.json_encode($result));
        $retServices = [];
        foreach ($result as $key=>$value)
        {
            //Log::debug(__METHOD__.'   key:'.$key.'  value:'.json_encode($value));

            array_push($retServices,$value['Address'].':'.$value['Port']);
        }

//       $services = [
//
//        ];

        Log::debug(__METHOD__.'  retServices:'.json_encode($retServices));

        return $retServices;
    }
}
