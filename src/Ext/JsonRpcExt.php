<?php
namespace DNMVCS\Ext;

use DNMVCS\SingletonEx;
use Exception;

class JsonRpcClientBase
{
    use SingletonEx;
    
    public function __call($method, $arguments)
    {
        $class=JsonRpcExt::G()->getRealClass($this);
        $ret=JsonRpcExt::G()->callRPC($class, $method, $arguments);
        return $ret;
    }
}
class JsonRpcExt
{
    use SingletonEx;
    const DEFAULT_OPTIONS=[
        'jsonrpc_namespace'=>'JsonRpc',
        'jsonrpc_backend'=>'https://127.0.0.1',
    ];
    public $is_inited;
    protected $backend;
    protected $prefix;
    public function init($options=[], $context)
    {
        $options=array_replace_recursive(static::DEFAULT_OPTIONS, $options);
        
        $namespace=$options['jsonrpc_namespace'];
        $this->backend=$options['jsonrpc_backend'];
        
        $this->prefix=trim($namespace, '\\').'\\';
        $this->is_inited=true;
        spl_autoload_register([$this,'_autoload']);
        
        return $this;
    }
    public function getRealClass($object)
    {
        $class=get_class($object);
        if (substr($class, 0, strlen($this->prefix))!==$this->prefix) {
            return $class;
        }
        return substr($class, strlen($this->prefix));
    }
    
    
    public function _autoload($class)
    {
        if (substr($class, 0, strlen($this->prefix))!==$this->prefix) {
            return;
        }
        $blocks=explode('\\', $class);
        $basename=array_pop($blocks);
        $namespace=implode('\\', $blocks);
        
        $code="namespace $namespace{ class $basename extends \\". __NAMESPACE__  ."\\JsonRpcClientBase{} }";
        eval($code);
    }
    public function callRpc($classname, $method, $arguments)
    {
        $post=[
           "jsonrpc"=>"2.0",
        ];
        $post['method']=str_replace("\\", ".", $classname."\\".$method);
        $post['params']=$arguments;
        
        $post['id']=time();
        $data=$this->curl_file_get_contents($this->backend, $post);
        $data=json_decode($data, true);
        if (empty($data)) {
            throw new Exception("failed", -1);
        }
        if (isset($data['error'])) {
            throw new Exception($ret['error']['message'], $ret['error']['code']);
        }
        return $data['result'];
    }
    
    public function onRpcCall(array $input)
    {
        $id=$input['id']??null;
        
        $a=explode('.', $input['method']);
        $method=array_pop($a);
        $service=implode("\\", $a);
        $args=$input['params']??[];
        $ret=[
           "jsonrpc"=>"2.0",
        ];
        try {
            //DN::ThrowOn()
            $ret['result']=$service::G()->$method(...$args);
        } catch (\Throwable $ex) {
            $ret['error']=[
                'code'=>$ex->getCode(),
                'message'=>$ex->getMessage(),
            ];
        }
        $ret['id']=$id;
        
        return $ret;
    }
    /////////////////////

    protected function curl_file_get_contents($url, $post)
    {
        $ch = curl_init();
        
        if (is_array($url)) {
            list($base_url, $real_host)=$url;
            $url=$base_url;
            $host=parse_url($url, PHP_URL_HOST);
            $port=parse_url($url, PHP_URL_PORT);
            $c=$host.':'.$port.':'.$real_host;
            curl_setopt($ch, CURLOPT_CONNECT_TO, [$c]);
        }
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
        
        $this->prepare_token($ch);
        
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }
    protected function prepare_token($ch)
    {
        return;
    }
}
