<?php
// bin/duckphp.php --create --namespace MyProject --prune-core --prune-helper --dest build --autoload-file ../autoload.php  --start
/////////////////

$longopts  = array(
    "help",
    "start",
    "create",
    "force",
    "full",
    "prune-core",
    "prune-helper",
    
    "namespace:",
    "dest:",
    "autoload-file:",
    'host:',
    'port:',
);
$cli_options = getopt('', $longopts);
$options=[];
$options['help']=isset($cli_options['help'])?true:false;
$options['start']=isset($cli_options['start'])?true:false;
$options['create']=isset($cli_options['create'])?true:false;
$options['force']=isset($cli_options['force'])?true:false;
$options['prune_helper']=isset($cli_options['prune-helper'])?true:false;
$options['prune_core']=isset($cli_options['prune-core'])?true:false;
$options['full']=isset($cli_options['full'])?true:false;


$options['namespace']=isset($cli_options['namespace'])?$cli_options['namespace']:'';
$options['dest']=isset($cli_options['dest'])?$cli_options['dest']:'';
$options['autoload_file']=isset($cli_options['autoload-file'])?$cli_options['autoload-file']:'';
$options['host']=isset($cli_options['host'])?$cli_options['host']:'';
$options['port']=isset($cli_options['port'])?$cli_options['port']:'';
C::RunQuickly($options);
return ;



class C
{
    public $options=[
        'prune_helper'=>false,
        'prune_core'=>false,
        'namespace' =>'MY',
        'src'=>'',
        'dest'=>'',
        'run'=>false,
    ];
    public function RunQuickly($options)
    {
        //$class=static::class;
        return (new static())->init($options)->run();
    }
    public function init($options)
    {
        $this->options=array_replace_recursive($this->options, $options);
        return $this;
    }
    public function run()
    {
        $this->showWelcome();
        if ($this->options['help']) {
            $this->showHelp();
            return;
        }
        $is_done=false;
        if ($this->options['create']) {
            $name=$this->options['full']?'demo':'template';
            $source= __DIR__ .'/../'.$name;
            $dest=realpath($this->options['dest']);
            $this->dumpDir($source, $dest, $this->options['force']);
            
            $is_done=true;
        }
        if ($this->options['start']) {
            echo "----------------------\n";
            echo "Start Inner PHP Server\n";
            echo "----------------------\n";
            $dest=realpath($this->options['dest']);
            $file=$dest.'/bin/start_server.php';
            $PHP='/usr/bin/env php ';
            $file=escapeshellcmd($file);
            $cmd=$PHP.$file;
            $cmd.=!empty($this->options['host'])?' --host='.escapeshellcmd($this->options['host']):'';
            $cmd.=!empty($this->options['port'])?' --port='.escapeshellcmd($this->options['port']):'';
            
            system($cmd);
            return;
        }
        if (!$is_done) {
            $this->showHelp();
            return;
        }
    }
    public function dumpDir($source, $dest, $force)
    {
        $source=realpath($source);
        $dest=realpath($dest);
        $directory = new \RecursiveDirectoryIterator($source, \FilesystemIterator::CURRENT_AS_PATHNAME | \FilesystemIterator::SKIP_DOTS);
        $iterator = new \RecursiveIteratorIterator($directory);
        $files = \iterator_to_array($iterator, false);
        echo "Copying file...\n";
        if(!$force){
            foreach ($files as $file) {
                $short_file_name=substr($file, strlen($source)+1);
                $dest_file=$dest.DIRECTORY_SEPARATOR.$short_file_name;
                if(is_file($dest_file)){
                    echo "file exists: $dest_file \n";
                    echo "use --force to overwrite existed files \n";
                    return false;
                }
            }
        }
        foreach ($files as $file) {
            $short_file_name=substr($file, strlen($source)+1);
            if ($short_file_name=='headfile/headfile.php') {
                continue;
            }
            if ($this->options['prune_helper']) {
                if ($this->pruneHelper($short_file_name)) {
                    echo "prune skip: $short_file_name \n";
                    continue;
                }
            }
            
            // mkdir.
            $blocks=explode(DIRECTORY_SEPARATOR, $short_file_name);
            array_pop($blocks);
            $full_dir=$dest;
            foreach ($blocks as $t) {
                $full_dir.=DIRECTORY_SEPARATOR.$t;
                if (!is_dir($full_dir)) {
                    mkdir($full_dir);
                }
            }
            
            $dest_file=$dest.DIRECTORY_SEPARATOR.$short_file_name;
            $data=file_get_contents($file);
            
            $data=$this->filteText($data);
            $data=$this->changeHeadFile($data);
            
            if ($this->options['prune_core']) {
                $data=$this->purceCore($data);
            }
            if ($this->options['namespace']) {
                $data=$this->filteNamespace($data);
            }
            ////
            file_put_contents($dest_file, $data);
            echo $dest_file;
            echo "\n";
            //decoct(fileperms($file) & 0777);
        }
    }
    protected function pruneHelper($short_file_name)
    {
        return false; //TODO  to work;
        if (substr($short_file_name, -strlen('Helper.php'))==='Helper.php') {
            return true;
        } else {
            return false;
        }
    }
    protected function filteText($data)
    {
        $data=str_replace('//* DuckPhp TO DELETE ', '/* DuckPhp HAS DELETE ', $data);
        $data=str_replace('/* DuckPhp TO KEEP ', '//* DuckPhp HAS KEEP ', $data);
        return $data;
    }
    protected function filteNamespace($data)
    {
        $namespace=$this->options['namespace'];
        if ($namespace==='MY') {
            return $data;
        }
        $data=str_replace('MY\\', $namespace.'\\', $data);
        return $data;
    }
    protected function changeHeadFile($data)
    {
        $autoload_file=$this->options['autoload_file']?$this->options['autoload_file']:"vendor/autoload.php";
        $str_header="require_once(__DIR__.'/../$autoload_file');";
        
        $data=str_replace("require_once(__DIR__.'/../headfile/headfile.php');", $str_header, $data);
        return $data;
    }
    protected function purceCore($data)
    {
        $data=str_replace("DuckPhp\\", "DuckPhp\\Core\\", $data);
        $data=str_replace("DuckPhp\\Core\\Core", "DuckPhp\\Core", $data);
        return $data;
    }
    protected function showWelcome()
    {
        echo <<<EOT
Well Come to use DuckPhp Manager , for more info , use --help \n
EOT;
    }
    protected function showHelp()
    {
        echo <<<EOT
----
--help       Show this help.

--create     Create the skeleton-project
  --namespace <namespace>   Use another project namespace.
  --force                   Overwrite exited files.
  --full                    Use The demo template
  --prune-core              Just use DuckPhp\Core ,but not use DuckPhp
  --prune-helper            Do not use the Helper class, 
  
  --autoload-file <path>    Use another autoload file.
  --dest [path]             Copy project file to here.
--start                     Start the server var bin/start_server.php
  --host [host]             Use this host
  --port [port]             Use this port
----
To start the project , use '--start' or run script 'bin/start_server.php'

EOT;
    }
}