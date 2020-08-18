<?php
/**
 * 应用初始化
 *
 * Created by PhpStorm.
 * User: Alex
 * Date: 2020-8-11
 * Time: 18:39
 */

namespace Sugaryesp;

use Sugaryesp\Library\Config;

class App
{

    protected static $instance;
    protected $config;

    private function __construct()
    {
        // 加载配置文件
        $this->config = new Config(CONF_PATH);
    }

    private function __clone()
    {

    }

    public static function getInstance()
    {
        if (!(self::$instance instanceof self)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * 调用指定控制器方法
     *
     * @throws \Exception
     */
    public static function run()
    {
        $get = $_GET;
        if (!isset($get['c'])) {
            $controller = self::getInstance()->config['app']['default_controller'];  // 默认的控制器
        } else {
            $controller = $get['c'];
        }

        if (!isset($get['v'])) {
            $v = self::getInstance()->config['app']['default_action'];
        } else {
            $v = $get['v'];
        }

        $cLow = strtolower($controller);
        $c = ucwords($cLow);  // 控制器首字母大写
        $class = '\\App\\Controller\\' . $c;  // \App\Controller\Home

        if (!class_exists($class)) {
            throw new \Exception('The controller class is not exists !');
        }
        if (!method_exists($class, $v)) {
            throw new \Exception('The controller don\'t have this method => ' . $v);
        }

        $obj = new $class($c, $v);  // 初始化控制器的名称和方法的名称

        $controllerConfigs = self::getInstance()->config['controller'];  // 获取控制器相关的配置项

        $confMiddlewares = [];  // 需要实例化的中间件
        // 所有的控制器都需要实现的中间件方法
        if (isset($controllerConfigs['global_middleware'])) {
            $confMiddlewares = array_merge($confMiddlewares, $controllerConfigs['global_middleware']);
        }
        // 指定控制器需要实现的中间件方法
        if (isset($controllerConfigs[$cLow]['middleware'])) {
            $confMiddlewares = array_merge($confMiddlewares, $controllerConfigs[$cLow]['middleware']);
        }

        $decoratorObjs = [];  // 装饰器
        foreach ($confMiddlewares as $middleware) {
            $decoratorObjs[] = new $middleware;  // 实例化定义的装饰器中间件
        }
        // 调用请求方法前的装饰器方法
        foreach ($decoratorObjs as $decoratorObj) {
            $decoratorObj->beforeRequest($obj);
        }

        $returnValue = $obj->$v();  // 调用具体的请求控制器方法

        // 调用请求方法后的装饰器方法
        foreach ($decoratorObjs as $decoratorObj) {
            $decoratorObj->afterRequest($returnValue);
        }

    }

}