<?php declare(strict_types=1);
use SimpleAuth\Base\Helper\ViewHelper as V;

V::startSection('content'); ?>
<div class="container">
<a href="<?=__url('register')?>">注册</a>
<form method="post" action="<?= V::H($url_login); ?>"
    <?= $csrf_field ?>
<?php if (isset($error)) { ?>
    <div><b>错误： <?= V::H($error);?></b></div>
<?php }?>
    <label><?= V::HL('用户名'); ?></label>
    <div>
        <input name="name" value="<?= V::H($name)?>" autofocus>
    </div>
    <label><?= V::HL('密码'); ?></label>
    <div>
        <input type="password" name="password">
    </div>
    <div>
        <button type="submit">
            <?= V::HL('登录'); ?>
        </button>
    </div>
</form>

<?php V::stopSection(); ?>
<?php V::Display('layouts/app');?>