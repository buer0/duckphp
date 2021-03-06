<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */

namespace DuckPhp\Helper;

use DuckPhp\Helper\HelperTrait;
use DuckPhp\Core\App;

class ControllerHelper
{
    use HelperTrait;
    
    public static function Setting($key)
    {
        return App::Setting($key);
    }
    public static function Config($key, $file_basename = 'config')
    {
        return App::Config($key, $file_basename);
    }
    public static function LoadConfig($file_basename)
    {
        return App::LoadConfig($file_basename);
    }
    ////
    public static function H($str)
    {
        return App::H($str);
    }
    public static function L($str, $args = [])
    {
        return App::L($str, $args);
    }
    public static function HL($str, $args = [])
    {
        return App::HL($str, $args);
    }
    public static function Display($view, $data = null)
    {
        return App::Display($view, $data);
    }
    public static function URL($url)
    {
        return App::URL($url);
    }
    public static function Domain()
    {
        return App::Domain();
    }
    ////
    public static function getParameters()
    {
        return App::getParameters();
    }
    public static function getRouteCallingMethod()
    {
        return App::getRouteCallingMethod();
    }
    public static function setRouteCallingMethod($method)
    {
        return App::setRouteCallingMethod($method);
    }
    public static function getPathInfo()
    {
        return App::getPathInfo();
    }
    public static function dumpAllRouteHooksAsString()
    {
        return App::dumpAllRouteHooksAsString();
    }
    ///////////////
    public static function Show($data = [], $view = null)
    {
        return App::Show($data, $view);
    }
    public static function setViewHeadFoot($head_file = null, $foot_file = null)
    {
        return App::setViewHeadFoot($head_file, $foot_file);
    }
    public static function assignViewData($key, $value = null)
    {
        return App::assignViewData($key, $value);
    }
    ////////////////////
    public static function ExitRedirect($url, $exit = true)
    {
        return App::ExitRedirect($url, $exit);
    }
    public static function ExitRedirectOutside($url, $exit = true)
    {
        return App::ExitRedirectOutside($url, $exit);
    }
    public static function ExitRouteTo($url, $exit = true)
    {
        return App::ExitRedirect(static::URL($url), $exit);
    }
    public static function Exit404($exit = true)
    {
        return App::Exit404($exit);
    }
    public static function ExitJson($ret, $exit = true)
    {
        return App::ExitJson($ret, $exit);
    }
    /////////////////
    public static function header($output, bool $replace = true, int $http_response_code = 0)
    {
        return App::header($output, $replace, $http_response_code);
    }
    public static function setcookie(string $key, string $value = '', int $expire = 0, string $path = '/', string $domain = '', bool $secure = false, bool $httponly = false)
    {
        return App::setcookie($key, $value, $expire, $path, $domain, $secure, $httponly);
    }
    public static function exit($code = 0)
    {
        return App::exit($code);
    }
    //exception manager
    public static function assignExceptionHandler($classes, $callback = null)
    {
        return App::assignExceptionHandler($classes, $callback);
    }
    public static function setMultiExceptionHandler(array $classes, $callback)
    {
        return App::setMultiExceptionHandler($classes, $callback);
    }
    public static function setDefaultExceptionHandler($callback)
    {
        return App::setDefaultExceptionHandler($callback);
    }
    public static function SG()
    {
        return App::SG();
    }
    public static function Parameter($key, $default = null)
    {
        return App::Parameter($key, $default);
    }
    public static function GET($key, $default = null)
    {
        return App::GET($key, $default);
    }
    public static function POST($key, $default = null)
    {
        return App::POST($key, $default);
    }
    public static function REQUEST($key, $default = null)
    {
        return App::REQUEST($key, $default);
    }
    public static function COOKIE($key, $default = null)
    {
        return App::COOKIE($key, $default);
    }
    public static function SERVER($key, $default = null)
    {
        return App::SERVER($key, $default);
    }
    ////
    public static function Pager($object = null)
    {
        return App::Pager($object);
    }
    public static function PageNo($new_value = null)
    {
        return App::PageNo($new_value);
    }
    public static function PageSize($new_value = null)
    {
        return App::PageSize($new_value);
    }
    public static function PageHtml($total, $options = [])
    {
        return  App::PageHtml($total, $options);
    }
}
