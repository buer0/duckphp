# DuckPhp\Core\ThrowOn

## 简介
快速抛出异常的 trait

## 方法
public static function ThrowOn($flag, $message, $code=0, $exception_class=null)

    如果 $flag成立，则抛出异常
    如果未指定 $exception_class，则判断当前类是否是 Exception 类的子类，并抛出。
    如果不是，则默认为 Exception 类，并抛出
## 详解

trait ThrowOn 是为了写代码更偷懒。


## 例子
```
class MyClass extends \Exception
{
    use \DuckPhp\Core\ThrowOn;
}
class X
{
    use \DuckPhp\Core\ThrowOn;
}
MyException::ThrowOn(true,"something exception",142857);
X::ThrowOn(true,"second",MyException::class);
X::ThrowOn(true,"third",22,MyException::class);
X::ThrowOn(true,"forth");

```

ThrowOn 的弊病是多了一层堆栈。调试的时候要注意。

