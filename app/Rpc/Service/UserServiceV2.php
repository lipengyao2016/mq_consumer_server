<?php declare(strict_types=1);


namespace App\Rpc\Service;


use App\Rpc\Lib\UserInterface;
use Exception;
use Swoft\Co;
use Swoft\Log\Helper\Log;
use Swoft\Rpc\Server\Annotation\Mapping\Service;

/**
 * Class UserServiceV2
 *
 * @since 2.0
 *
 * @Service(version="1.2")
 */
class UserServiceV2 implements UserInterface
{
    /**
     * @param int   $id
     * @param mixed $type
     * @param int   $count
     *
     * @return array
     */
    public function getList(int $id, $type, int $count = 10): array
    {
        Log::debug(__METHOD__.' id:'.$id.' type:'.$type.' pid:'.posix_getpid());
        return [
            'name' => ['list'],
            'v'    => '1.2'
        ];
    }

    /**
     * @return void
     */
    public function returnNull(): void
    {
        Log::debug(__METHOD__.' called!');
        return;
    }

    /**
     * @param int $id
     *
     * @return bool
     */
    public function delete(int $id): bool
    {
        Log::debug(__METHOD__.' id:'.$id);
        return false;
    }

    /**
     * @return string
     */
    public function getBigContent(): string
    {
        $content = Co::readFile(__DIR__ . '/big.data');
        Log::debug(__METHOD__.' bigCotent len:'.strlen($content));
        return $content;
    }

    /**
     * Exception
     * @throws Exception
     */
    public function exception(): void
    {
        Log::debug(__METHOD__.' called!');
        throw new Exception('exception version2');
    }

    /**
     * @param string $content
     *
     * @return int
     */
    public function sendBigContent(string $content): int
    {
        Log::debug(__METHOD__.' content len:'.strlen($content));
        return strlen($content);
    }
}