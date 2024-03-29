<?php declare(strict_types=1);


namespace App\Process;


use App\Model\Entity\User;
use Swoft\Db\Exception\DbException;
use Swoft\Log\Helper\CLog;
use Swoft\Log\Helper\Log;
use Swoft\Process\Annotation\Mapping\Process;
use Swoft\Process\Contract\ProcessInterface;
use Swoft\Redis\Redis;
use Swoole\Coroutine;
use Swoole\Process\Pool;

/**
 * Class Worker2Process
 *
 * @since 2.0
 *
 * @Process()
 */
class Worker2Process implements ProcessInterface
{
    /**
     * @param Pool $pool
     * @param int  $workerId
     *
     * @throws DbException
     */
    public function run(Pool $pool, int $workerId): void
    {
        while (true) {

            // Database
            $user = User::find(1)->toArray();
            Log::info('user='.json_encode($user).' pid:'.posix_getpid().' workerId:'.$workerId);

            // Redis
            Redis::set('test', 'ok');
            Log::info('test='.Redis::get('test'));

            Log::info('worker-' . $workerId.' context='.context()->getWorkerId());

            Coroutine::sleep(5);
        }
    }
}
