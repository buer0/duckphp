<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */

//dvaknheo@github.com
//OK，Lazy
namespace DuckPhp;

use DuckPhp\Core\App;
use DuckPhp\Ext\DBManager;
use DuckPhp\Ext\Pager;
use DuckPhp\Ext\RouteHookRouteMap;
use DuckPhp\Ext\RouteHookOneFileMode;

class DuckPhp extends App
{
    const VERSION = '1.2.5';
    
    public function __construct()
    {
        parent::__construct();
        $options = [
            'mode_no_path_info' => false,
            'log_sql_query' => false,
            'log_sql_level' => 'debug',
            'db_before_query_handler' => [static::class, 'OnQuery']
        ];
        
        /* no use
        if (PHP_SAPI === 'cli' && extension_loaded('swoole')) {
            //$t = ['DuckPhp\Ext\PluginForSwooleHttpd' => true];
            //$options['ext'] = array_merge($t, $options); // make it first
        }
        */
        $this->options = array_merge($options, $this->options);
        $ext = [
            DBManager::class => true,
            RouteHookRouteMap::class => true,
        ];
        $this->options['ext'] = array_merge($ext, $this->options['ext']);
    }
    //@override
    protected function initOptions($options = [])
    {
        parent::initOptions($options);
        if ($this->options['mode_no_path_info'] ?? false) {
            $this->options['ext'][RouteHookOneFileMode::class] = $this->options['ext'][RouteHookOneFileMode::class] ?? true;
        }
    }
    //@override
    public static function OnQuery($db, $sql, ...$args)
    {
        return static::G()->_OnQuery($db, $sql, ...$args);
    }
    //@override
    public function _OnQuery($db, $sql, ...$args)
    {
        if (!$this->options['log_sql_query']) {
            DBManager::G()->setBeforeQueryHandler($db, null);
            return;
        }
        static::Logger()->log($this->options['log_sql_level'], '[sql]: ' . $sql, $args);
    }
    //@override
    public function _Pager($object = null)
    {
        $pager = Pager::G($object);
        $pager->options['pager_context_class'] = static::class;
        return $pager;
    }
    //@override
    public function _DB($tag)
    {
        return DBManager::G()->_DB($tag);
    }
    //@override
    public function _DB_R()
    {
        return DBManager::G()->_DB_R();
    }
    //@override
    public function _DB_W()
    {
        return DBManager::G()->_DB_W();
    }
}
