<?php
namespace DNMVCS\Ext;

use DNMVCS\Basic\SingletonEx;
use DNMVCS\Basic\SuperGlobal;
use DNMVCS\Inneer\RouteHookRewrite;

class RouteHookDirectoryMode
{
    use SingletonEx;
    const DEFAULT_OPTIONS=[
        'mode_dir_index_file'=>'',
        'mode_dir_use_path_info'=>true,
        'mode_dir_key_for_module'=>true,
        'mode_dir_key_for_action'=>true,
    ];
    public function init($options=[], $context=null)
    {
        $this->basepath=$options['mode_dir_basepath'];
        if ($context) {
            $context->addRouteHook([static::G(),'hook']);
        }
    }
    protected function adjustPathinfo($path_info, $document_root)
    {
        //$this->basepath=ltrim($this->basepath,'/').'/';
        $basepath=$this->basepath;
        $input_path=parse_url(SuperGlobal::G()->_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $script_filename=SuperGlobal::G()->_SERVER['SCRIPT_FILENAME'];
        $path_info=substr($document_root.$input_path, strlen($basepath));
        $path_info=ltrim($path_info, '/').'/';
        $blocks=explode('/', $path_info);

        $path_info='';
        $has_file=false;
        foreach ($blocks as $i=>$v) {
            if (!$has_file && substr($v, -strlen('.php'))==='.php') {
                $has_file=true;
                $path_info.=substr($v, 0, -strlen('.php')).'/';
                if (!($blocks[$i+1])) {
                    $path_info.='index';
                    break;
                }
            } else {
                $path_info.=$v.'/';
            }
        }
        $path_info=rtrim($path_info, '/');
        
        return $path_info;
    }
    public function onURL($url=null)
    {
        if (strlen($url)>0 && '/'==$url{0}) {
            return $url;
        };
        
        $url=RouteHookRewrite::G()->filteRewrite($url);
        
        $document_root=SuperGlobal::G()->_SERVER['DOCUMENT_ROOT'];
        $base_url=substr($this->basepath, strlen($document_root));
        $input_path=parse_url($url, PHP_URL_PATH);
        
        $blocks=explode('/', $input_path);
        
        $basepath=$this->basepath;
        $new_path='';
        $l=count($blocks);
        foreach ($blocks as $i=> $v) {
            if ($i+1>=$l) {
                break;
            }
            $class_names=array_slice($blocks, 0, $i+1);
            $file=$basepath.'/'.implode('/', $class_names).'.php';
            $path_info=isset($blocks[$i])?array_slice($blocks, -$i-1):[];
            $path_info=implode('/', $path_info);
            if (is_file($file)) {
                $new_path=$base_url.'/'.implode('/', $class_names).'.php'.($path_info?'/'.$path_info:'');
            }
        }
        if (!$new_path) {
            return $new_path;
        }
    
        $new_get=[];
        parse_str(parse_url($url, PHP_URL_QUERY), $new_get);
        
        $get=array_merge($new_get, $new_get);
        $query=$get?'?'.http_build_query($get):'';
        $ret=$new_path.$query;
        return $ret;
    }
    // abc/d/e.php/g/h?act=z  abc/d/e/g
    public function hook($route)
    {
        $route->setURLHandler([$this,'onURL']); //todo once ?
        
        $route->path_info=$this->adjustPathinfo($route->path_info, $route->document_root);
        $route->calling_path=$route->path_info;
    }
}