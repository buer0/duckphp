<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */

namespace DuckPhp\Core;

use DuckPhp\Core\ComponentInterface;
use DuckPhp\Core\SingletonEx;

class Configer extends ComponentBase
{
    public $options = [
        'path' => '',
        'path_config' => 'config',
        
        'setting' => [],
        'all_config' => [],
        'setting_file' => 'setting',
        'skip_setting_file' => false,
        'skip_env_file' => true,
        'config_ext_files' => [],
    ];
    protected $base_path;
    protected $path;
    protected $is_setting_inited = false;
    protected $all_config = [];
    protected $setting = [];
    
    //@override
    protected function initOptions(array $options)
    {
        $this->base_path = $this->options['path'] ?? '';
        
        $this->path = parent::getComponenetPathByKey('path_config');
        
        $this->setting = $this->options['setting'] ?? [];
        $this->all_config = $this->options['all_config'] ?? [];
    }
    
    public function _Setting($key)
    {
        if ($this->is_setting_inited) {
            return $this->setting[$key] ?? null;
        }
        if (!$this->options['skip_env_file']) {
            $env_setting = parse_ini_file(realpath($this->base_path).'/.env');
            $env_setting = $env_setting?:[];
            $this->setting = array_merge($this->setting, $env_setting);
        }
        if (!$this->options['skip_setting_file']) {
            $setting_file = $this->options['setting_file'];
            $full_setting_file = $this->path.$setting_file.'.php';
            if (!is_file($full_setting_file)) {
                $this->exitWhenNoSettingFile($full_setting_file, $setting_file); // @codeCoverageIgnore
            }
            $setting = $this->loadFile($full_setting_file);
            $this->setting = array_merge($this->setting, $setting);
        }
        $this->is_setting_inited = true;
        return $this->setting[$key] ?? null;
    }
    protected function exitWhenNoSettingFile($full_setting_file, $setting_file)
    {
        echo "<h1> Class '". static::class."' Fatal: No setting file[ ".$full_setting_file.' ]!</h1>'; // @codeCoverageIgnoreStart
        echo '<h2>change '.$setting_file.'.sample.php to '. $setting_file.".php !</h2>";
        echo "<h2> Or turn on  options ['skip_setting_file']</h2>"; //
        exit; // @codeCoverageIgnoreEnd
    }
    
    public function _Config($key, $file_basename = 'config')
    {
        $config = $this->_LoadConfig($file_basename);
        return isset($config[$key])?$config[$key]:null;
    }
    public function _LoadConfig($file_basename = 'config')
    {
        if (isset($this->all_config[$file_basename])) {
            return $this->all_config[$file_basename];
        }
        $full_file = $this->path.$file_basename.'.php';
        if (isset($this->options['config_ext_files'][$file_basename]) && !is_file($full_file)) {
            $full_file = $this->options['config_ext_files'][$file_basename];
            $config = $this->loadFile($full_file);
        } else {
            $config = $this->loadFile($full_file);
        }
        $this->all_config[$file_basename] = $config;
        return $config;
    }
    public function assignExtConfigFile($key, $value = null)
    {
        if (is_array($key) && $value === null) {
            $this->options['config_ext_files'] = array_merge($this->options['config_ext_files'], $key);
        } else {
            $this->options['config_ext_files'][$key] = $value;
        }
    }
    protected function loadFile($file)
    {
        return require $file;
    }
}
