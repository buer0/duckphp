# DuckPhp\Ext\Pager
[toc]
## 简介
`组件类` 分页类 符合接口  DuckPhp\Ext\PagerInterface

## 选项

    - 'context_class' => null
    - 'current' => null

    - 'rewrite' => null
    - 'url' => null


'url' => null,

    url
'current' => null,

    当前页码
'page_size' => 30,

    每页长度
'page_key' => 'page',

    默认分页的key
'rewrite' => null,

    重写函数，替代 defaultGetUrl
'context_class' => null,

    指定提供 SG 方法替代超全局函数的类
'pager_context_class' => ''

    设置 context 的class
## 公开方法

### 组件方法
public function __construct()

public function init(array $options, object $context = null)

public function isInited():bool

### PagerInterface 方法

public function current($new_value= null)

public function pageSize($new_value = null)

public function render($total, $options = [])


### 其他方法

获得当前总页数
public function getPageCount($total)

public function getUrl($page)

    根据页码ID
public function defaultGetUrl($page)

    默认的获得 URL 的方法

## 详解


Pager 类并没有像其他扩展那样初始化，而是在调用 App::Pager() 的时候 得到类。

Page 在 render($total, $options = []) 的时候会初始化一遍

App::Pager 得到的就是这个类 在得到这个类后，会填充 context_class
