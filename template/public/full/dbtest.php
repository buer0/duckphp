<?php
require(__DIR__.'/../../../autoload.php');  // @DUCKPHP_HEADFILE

use App as M;
use App as C;
use App as V;

use DuckPhp\Core\SingletonEx;
use DuckPhp\Ext\EmptyView;

class App extends \DuckPhp\DuckPhp
{
    // @override
    public $options = [
        'is_debug' => true,
            // 开启调试模式
        'skip_setting_file' => true,
            // 本例特殊，跳过设置文件 这个选项防止没有上传设置文件到服务器
        'mode_no_path_info' => true,
            // 单一文件模式
        
        'namespace_controller'=>"\\",   
            // 本例特殊，设置控制器的命名空间为根
        'ext' => [
            EmptyView::class => true,
            // 我们用扩展 EmptyView 代替系统的 View
        ],
        'setting'=>[
        //数据库设置
            'database_list' => [
                [
                    'dsn' => 'mysql:host=127.0.0.1;port=3306;dbname=DnSample;charset=utf8mb4;',
                    'username' => 'admin',
                    'password' => '123456',
                    'driver_options' => [],
                ],
            ],
        ],
    ];
    //视图数据
    public static function GetViewData()
    {
        return EmptyView::G()->data;
    }
}
//业务服务
class MyService
{
    use SingletonEx;
    public function getDataList($page,$pagesize)
    {
        return MyModel::getDataList($page,$pagesize);
    }
    public function getData($id)
    {
        return MyModel::getData($id);
    }
    public function addData($data)
    {
        return MyModel::addData($data);
    }
    public function updateData($id,$data)
    {
        return MyModel::updateData($id,$data);
    }
    public function deleteData($id)
    {
        return MyModel::deleteData($id);
    }
}

class MyModel
{
    public static function getDataList($page,$pagesize)
    {
        $sql="select * from test order by id desc";
        $total=M::DB()->fetchColumn(M::SqlForCountSimply($sql));
        $list=M::DB()->fetchAll(M::SqlForPager($sql,$page,$pagesize));

        return [$total,$list];
    }
    public static function getData($id)
    {
        $sql="select * from test where id=?";
        return M::DB()->fetch($sql,$id);
    }
    public static function addData($data)
    {
        $sql="insert test (content) values(?)";
        M::DB()->execute($sql,$data['content']);
        return M::DB()->lastInsertId();
    }
    public static function updateData($id,$data)
    {
        $sql="update test set content = ? where id=?";
        $flag=M::DB()->execute($sql,$data['content'],$id);
        return $flag;
    }
    public static function deleteData($id)
    {
        $sql="delete from test where id=? limit 1";
        M::DB()->execute($sql,$id);
    }
}
/////////////////////////////////////////
class Main
{
    public function index()
    {
        list($total,$list)=MyService::G()->getDataList(C::PageNo(),C::PageSize(3));
        $pager=C::PageHtml($total);
        C::Show(get_defined_vars(),'main_view');
    }
    public function do_index()
    {
        MyService::G()->addData(C::SG()->_POST);
        $this->index();
    }
    public function show()
    {
        $data=MyService::G()->getData(C::SG()->_GET['id']??0);
        C::Show(get_defined_vars(),'show');
    }
    public function do_show()
    {
        MyService::G()->updateData(C::SG()->_POST['id'],C::SG()->_POST);
        $this->show();
    }
    public function delete()
    {
        MyService::G()->deleteData(C::SG()->_GET['id']??0);
        C::ExitRouteTo('');
    }
}
///////////////

    App::RunQuickly(['error_404'=>function(){(new Main)->index();}]); // 如果 404 那就回主页
    $data = App::GetViewData();
    extract($data);
    if($skip_head_foot??false){
?>
        <html>
            <head>
            </head>
            <body>
            <header style="border:1px gray solid;">I am Header</header>
<?php
    }
    if($view=='main_view'){
?>
        <h1>数据</h1>
        <table>
            <tr><th>ID</th><th>内容</th></tr>
<?php
        foreach($list as $v){
?>
            <tr>
                <td><?=$v['id']?></td>
                <td><?=__h($v['content'])?></td>
                <td><a href="<?=__url('show?id='.$v['id'])?>">编辑</a></td>
                <td><a href="<?=__url('delete?id='.$v['id'])?>">删除</a></td>
            </tr>
<?php
        }
?>
        </table>
        <?=$pager?>
        <h1>新增</h1>
        <form method="post" action="<?=__url('')?>">
            <input type="text" name="content">
            <input type="submit">
        </form>
<?php
    }
    if($view=='show'){
        ?>
        <h1>查看/编辑</h1>
        原内容
        <p><?=__h($data['content'])?></p>
        <form method="post">
            <input type="hidden" name="id" value="<?=$data['id']?>">
            <input type="text" name="content" value="<?=__h($data['content'])?>">
            <input type="submit" value="编辑">
        </form>
        <a href="<?=__url('')?>">回首页</a>

<?php
    }
    if($skip_head_foot??false){
        ?>
        <footer style="border:1px gray solid;">I am footer</footer>
    </body>
</html>
<?php
    }
