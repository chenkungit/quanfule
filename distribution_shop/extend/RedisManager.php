<?php

/**
 * RedisManager
 * key and string
 * @method bool set($key, $value, $array = null)
 * @method string get($key)
 * @method string delete($key)
 * @method bool exists($key)
 * @method int expire(string $key, int $ttl)
 * @method int append($key, $value)
 * @method int decr($key)
 * @method int decrBy($key, $value)
 * @method string getRange($key, $start, $end)
 * @method int incr($key)
 * @method int incrBy($key, $value)
 * @method float incrByFloat($key, $increment)
 * @method int strlen($key)
 * @method array mget(array $array)
 * @method bool mset(array $array)
 * hash
 * @method int hSet($key, $hashKey, $value)
 * @method bool hSetNx($key, $hashKey, $value)
 * @method string hGet($key, $hashKey)
 * @method int hLen($key)
 * @method int hDel($key, $hashKey1, $hashKey2 = null, $hashKeyN = null)
 * @method array hKeys($key)
 * @method array hVals($key)
 * @method array hGetAll($key)
 * @method bool hExists($key, $hashKey)
 * @method bool hIncrBy($key, $hashKey, $value)
 * @method bool hIncrByFloat($key, $field, $increment)
 * @method bool hMset($key, $hashKeys)
 * @method array hMGet($key, $hashKeys)
 * list
 * @method array brPop(array $keys, $timeout)
 * @method array blPop(array $keys, $timeout)
 * @method int lLen($key)
 * @method int lPush($key, $value1, $value2 = null, $valueN = null)
 * @method string lPop($key)
 * @method array lRange($key, $start, $end)
 * @method int lRem($key, $value, $count)
 * @method bool lSet($key, $index, $value)
 * @method int rPush($key, $value1, $value2 = null, $valueN = null)
 * @method string rPop($key)
 * set
 * @method int sAdd($key, $value1, $value2 = null, $valueN = null)
 * @method array|bool scan(&$iterator, $pattern = null, $count = 0)
 * @method int sCard($key)
 * @method array sDiff($key1, $key2, $keyN = null)
 * @method array sInter($key1, $key2, $keyN = null)
 * @method int sInterStore($dstKey, $key1, $key2, $keyN = null)
 * @method int sDiffStore($dstKey, $key1, $key2, $keyN = null)
 * @method array sMembers($key)
 * @method bool sMove($srcKey, $dstKey, $member)
 * @method bool sPop($key)
 * @method string|array sRandMember($key, $count = null)
 * @method int sRem($key, $member1, $member2 = null, $memberN = null)
 * @method array sUnion($key1, $key2, $keyN = null)
 * @method int sUnionStore($dstKey, $key1, $key2, $keyN = null)
 * @method int sismember($key, $value)
 * sort
 * @method int zAdd($key, $score1, $value1, $score2 = null, $value2 = null, $scoreN = null, $valueN = null)
 * @method array zRange($key, $start, $end, $withscores = null)
 * @method int zRem($key, $member1, $member2 = null, $memberN = null)
 * @method array zRevRange($key, $start, $end, $withscore = null)
 * @method array zRangeByScore($key, $start, $end, array $options = array())
 * @method array zRangeByLex($key, $min, $max, $offset = null, $limit = null)
 * @method int zCount($key, $start, $end)
 * @method int zRemRangeByScore($key, $start, $end)
 * @method int zRemRangeByRank($key, $start, $end)
 * @method int zCard($key)
 * @method float zScore($key, $member)
 * @method int zRank($key, $member)
 * @method float zIncrBy($key, $value, $member)
 * @method int zUnion($Output, $ZSetKeys, array $Weights = null, $aggregateFunction = 'SUM')
 * @method int zInter($Output, $ZSetKeys, array $Weights = null, $aggregateFunction = 'SUM')
 * pub/sub
 * @method int publish($channel, $message)
 * @method string|array psubscribe($patterns, $callback)
 * @method string|array subscribe($channels, $callback)
 * @method array|int pubsub($keyword, $argument)
 * script
 * @method mixed eval($script, $args = array(), $numKeys = 0)
 * @method mixed evalSha($scriptSha, $args = array(), $numKeys = 0)
 * @method mixed script($command, $script)
 * @method string getLastError()
 * @method bool clearLastError()
 */
class RedisManager
{
    private static $instances = [];


    public static function getInstance($database)
    {

        if (!isset(static::$instances[$database])) {

            static::$instances[$database] = new \Redis();
            $configRedis = \think\facade\Config::get('redis.');
            if ($configRedis) {

                $host = $configRedis['host'];
                $port = isset($configRedis['port']) ? $configRedis['port'] : 0;
                $auth = isset($configRedis['password']) ? $configRedis['password'] : false;

                // 连接方式
                static::$instances[$database]->connect($host, $port);
//                if (strpos($host, 'sock') !== false) {
//                    static::$instances[$instance]->pconnect($host);
//                } else {
//                    static::$instances[$instance]->pconnect($host, $port);
//                }

                // 是否授权
                if ($auth) {
                    static::$instances[$database]->auth($auth);
                }

                if ($database) {
                    static::$instances[$database]->select($database);
                }

            } else {
                throw new Exception('未找到redis配置');
            }
        }

        return static::$instances[$database];
    }

    private function __clone()
    {
    }  //覆盖__clone()方法，禁止克隆
}

/**
 * @return Redis
 */

