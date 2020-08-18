<?php
/**
 * Created by PhpStorm.
 * User: Alex
 * Date: 2020-8-7
 * Time: 15:51
 */

// 定义应用根目录 eg: 'H:\semir\php\www.semirbiz.com\public\Alex'
define('ROOT_PATH', __DIR__);

include ROOT_PATH . '/Sugaryesp/Base.php';  // 包含初始化常量文件

include ROOT_PATH . '/Sugaryesp/Loader.php';  // 加载自动包含 php 文件的函数

include ROOT_PATH . '/Sugaryesp/Library/helper.php';  // 加载助手函数

// 执行一个没有 「包含」 的 php 文件时，会自动执行以下 spl_autoload_register 函数
// 比如当执行 \App\Controller\Home::index(); 时，注意，此时并没有 require 或者 include => H:\semir\php\www.semirbiz.com\public\Alex\App\Controller\Home.php
spl_autoload_register('\\Sugaryesp\\Loader::autoload');

// 启动应用
\Sugaryesp\App::run();