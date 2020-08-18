<?php
/**
 * 实现自动包含 php 文件
 *
 * Created by PhpStorm.
 * User: Alex
 * Date: 2020-8-11
 * Time: 17:00
 */

namespace Sugaryesp;

class Loader
{
    /**
     * 包含 .php 文件
     * $class 为所需包含的类的名称
     *
     * @param $class  eg => 'App\Controller\Home'
     */
    public static function autoload($class)
    {
        // require 的内容为 ： 比如 => 'H:\semir\php\www.semirbiz.com\public\Alex\App/Controller/Home.php'
        require ROOT_PATH . DS . str_replace('\\', '/', $class) . EXT;
    }

}