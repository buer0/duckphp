<?php
namespace DNMVCS\Ext;

use DNMVCS\SingletonEx;
use DNMVCS\SuperGlobal;

class RouteHookRewrite
{
    use SingletonEx;
    const DEFAULT_OPTIONS=[
        'rewrite_map'=>[],
    ];
    protected $rewrite_map=[];
    public function init($options=[], $context=null)
    {
        $this->rewrite_map=array_merge($this->rewrite_map, $options['rewrite_map']??[]);
        
        if ($context) {
            $context->addRouteHook([static::class,'Hook'], true);
            // $context->extendClassMethodByThirdParty(static::class,[],['assignRewrite','getRewrites']);
        }
    }
    public function assignRewrite($key, $value=null)
    {
        if (is_array($key)&& $value===null) {
            $this->rewrite_map=array_merge($this->rewrite_map, $key);
        } else {
            $this->rewrite_map[$key]=$value;
        }
    }
    public function getRewrites()
    {
        return $this->rewrite_map;
    }
    
    public function replaceRegexUrl($input_url, $template_url, $new_url)
    {
        if (substr($template_url, 0, 1)!=='~') {
            return null;
        }
        
        $input_path=parse_url($input_url, PHP_URL_PATH);
        $input_get=[];
        parse_str(parse_url($input_url, PHP_URL_QUERY), $input_get);
        
        //$template_path=parse_url($template_url,PHP_URL_PATH);
        //$template_get=[];
        parse_str(parse_url($template_url, PHP_URL_QUERY), $template_get);
        $p='/'.str_replace('/', '\/', substr($template_url, 1)).'/A';
        if (!preg_match($p, $input_path)) {
            return null;
        }
        //if(array_diff_assoc($input_get,$template_get)){ return null; }
        
        $new_url=str_replace('$', '\\', $new_url);
        $new_url=preg_replace($p, $new_url, $input_path);
        
        $new_path=parse_url($new_url, PHP_URL_PATH);
        $new_get=[];
        parse_str(parse_url($new_url, PHP_URL_QUERY), $new_get);
        
        $get=array_merge($input_get, $new_get);
        $query=$get?'?'.http_build_query($get):'';
        return $new_path.$query;
    }
    public function replaceNormalUrl($input_url, $template_url, $new_url)
    {
        if (substr($template_url, 0, 1)==='~') {
            return null;
        }
        
        $input_path=parse_url($input_url, PHP_URL_PATH);
        $input_get=[];
        parse_str(parse_url($input_url, PHP_URL_QUERY), $input_get);
        
        $template_path=parse_url($template_url, PHP_URL_PATH);
        $template_get=[];
        parse_str(parse_url($template_url, PHP_URL_QUERY), $template_get);
        
        if (array_diff_assoc($input_get, $template_get)) {
            return null;
        }
        
        $new_path=parse_url($new_url, PHP_URL_PATH);
        $new_get=[];
        parse_str(parse_url($new_url, PHP_URL_QUERY), $new_get);
        if ($input_path!==$template_path) {
            return null;
        }
        
        $get=array_merge($input_get, $new_get);
        $query=$get?'?'.http_build_query($get):'';
        
        return $new_path.$query;
    }
    public function filteRewrite($input_url)
    {
        foreach ($this->rewrite_map as $template_url=>$new_url) {
            $ret=$this->replaceNormalUrl($input_url, $template_url, $new_url);
            if ($ret!==null) {
                return $ret;
            }
            $ret=$this->replaceRegexUrl($input_url, $template_url, $new_url);
            if ($ret!==null) {
                return $ret;
            }
        }
        return null;
    }
    protected function changeRouteUrl($route, $url)
    {
        $path=parse_url($url, PHP_URL_PATH);
        $input_get=[];
        parse_str(parse_url($url, PHP_URL_QUERY), $input_get);
        $route->path_info=$path;
        SuperGlobal::G()->_SERVER['init_get']=SuperGlobal::G()->_GET;
        SuperGlobal::G()->_GET=$input_get;
    }
    protected function _Hook($route)
    {
        $path_info=$route->path_info;
        $uri=SuperGlobal::G()->_SERVER['REQUEST_URI'];
        $query=parse_url($uri, PHP_URL_QUERY);
        $query=$query?'?'.$query:'';
        $input_url=$path_info.$query;
        foreach ($this->rewrite_map as $template_url=>$new_url) {
            $url=$this->replaceNormalUrl($input_url, $template_url, $new_url);
            if ($url!==null) {
                $this->changeRouteUrl($route, $url);
            }
            $url=$this->replaceRegexUrl($input_url, $template_url, $new_url);
            if ($url!==null) {
                $this->changeRouteUrl($route, $url);
            }
        }

        return  null;
    }
    public static function Hook($route)
    {
        return static::G()->_Hook($route);
    }
}