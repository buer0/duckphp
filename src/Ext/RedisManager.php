<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */

namespace DuckPhp\Ext;

use DuckPhp\Core\ComponentBase;
use DuckPhp\Ext\RedisSimpleCache;
use Redis;

class RedisManager extends ComponentBase
{
    /*
    redis_lis=>
    [[
                'host'=>'',
                'port'=>'',
                'auth'=>'',
                'select'=>'',
            ]
    */
    public $options = [
        'redis_list' => null,
        'use_context_redis_setting' => true,
        'enable_simple_cache' => true,
        'simple_cache_prefix' => '',
    ];
    const TAG_WRITE = 0;
    const TAG_READ = 1;
    protected $pool = [];
    protected $redis_config_list = [];
    //@override
    protected function initOptions(array $options)
    {
        $this->redis_config_list = $this->options['redis_list'];
        if ($this->options['enable_simple_cache']) {
            RedisSimpleCache::G()->init([
                'redis' => $this->getServer(),
                'redis_cache_prefix' => $this->options['simple_cache_prefix']
            ]);
        }
    }
    //@override
    protected function initContext(object $context)
    {
        if ($this->options['use_context_redis_setting']) {
            $redis_list = get_class($context)::Setting('redis_list') ?? null;
            if (!isset($redis_list)) {
                $redis_list = isset($context->options) ? ($context->options['redis_list'] ?? null) : null;
            }
            if ($redis_list) {
                $this->redis_config_list = $redis_list;
            }
        }
        if (method_exists($context, 'extendComponents')) {
            $context->extendComponents(['Redis' => [static::class, 'Redis']], ['S','A']);
        }
        if ($this->options['enable_simple_cache']) {
            if (method_exists($context, 'extendComponents')) {
                $context->extendComponents(['SimpleCache' => [static::class, 'SimpleCache']], ['S','A']);
            }
        }
    }
    public static function Redis($tag = 0)
    {
        return static::G()->getServer($tag);
    }
    public static function SimpleCache()
    {
        return RedisSimpleCache::G();
    }
    public function getServer($tag = 0)
    {
        if (!isset($this->pool[$tag])) {
            $this->pool[$tag] = $this->createServer($this->redis_config_list[$tag]);
        }
        return $this->pool[$tag];
    }
    public function createServer($config)
    {
        $redis = new Redis();
        $redis->connect($config['host'], (int)$config['port']);
        if (isset($config['auth'])) {
            $redis->auth($config['auth']);
        }
        if (isset($config['select'])) {
            $redis->select((int)$config['select']);
        }
        return $redis;
    }
}
