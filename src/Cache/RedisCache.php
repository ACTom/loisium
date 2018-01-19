<?php

namespace loisium\Cache;

use loisium\Config;
use Redis;

class RedisCache implements ICache {
    private $redis = null;

    public function __construct(Config $config) {
        $this->redis = new Redis();
        if (!$this->redis->connect(
            $config->get('host', '127.0.0.1'),
            $config->get('port', 6379),
            $config->get('timeout', 0.0))) {
            throw new \RedisException('连接Redis失败！');
        };
        if ($config->get('auth', '') !== '') {
            if (!$this->redis->auth($config->get('auth'))) {
                throw new \RedisException('Redis授权验证失败！');
            }
        }
    }

    public function set($key, $value) {
        $this->redis->set($key, $value);
    }

    public function get($key) {
        return $this->redis->get($key);
    }

    public function push($stack, $value) {
        $this->redis->rPush($stack, $value);
    }

    public function pop($stack) {
        return $this->redis->lPop($stack);
    }

    public function count($stack) {
        return $this->redis->lLen($stack);
    }

}