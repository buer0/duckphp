<?php declare(strict_types=1);
// use MY\Base\Helper\ViewHelper as V;
    
    // change this file if you can
    $skip_in_full = true;
    $skip_in_full = false;  // @DUCKPHP_KEEP_IN_FULL
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <title>Hello DuckPHP!</title>
</head>
<body>
<h1>Hello DuckPHP</h1>
Now is [<?=$var?>]
<hr/>
<div>
    欢迎使用 DuckPHP ,<?php echo $var;?>
    <a href="<?=__url('test/done')?>">查看 Demo 结果</a>
</div>
<hr />
<?php
    if ($skip_in_full) {
        ?>
<div>
    请使用安装选项 --full 以打开开启 <a href="javascript:;">完整演示</a>
</div>
<?php
    }
?>
<div>
所有例子
<ul>
    <li><a href="/demo.php"> demo.php 单一文件演示所有操作</a>
    <li><a href="/helloworld.php"> helloworld.php 常见的 helloworld</a>
    <li><a href="/just-route.php">just-route.php 只要路由</a>
</ul>
完整模式下的其他例子
<ul>
    <li><a href="/full/auth.php">auth.php 简单的用户验证系统</a>
    <li><a href="/full/blog.php">blog.php 简单的博客</a>
    <li><a href="/full/dbtest.php">dbtest.php 数据库演示</a>
    <li><a href="/full/index.php">index.php 待整理，旧的自声明文档</a>
    <li><a href="/full/traditional.php">traditional.php 传统模式,一个文件解决，不折腾那么多 </a>
    <li><a href="/full/rpc.php">一个远程调用 json rpc 的例子 </a>
</ul>
</div>
</body>
</html>