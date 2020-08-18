<?php
/**
 * 定义需要使用的常量
 *
 * Created by PhpStorm.
 * User: Alex
 * Date: 2020-8-11
 * Time: 16:34
 */

// 文件夹分隔符
define('DS', DIRECTORY_SEPARATOR);
// 定义应用目录 => H:\semir\php\www.semirbiz.com\public\Alex\App\
define('APP_PATH', ROOT_PATH . DS . 'App' . DS);
// 定义框架版本号
define('ALEX_VERSION', '1.0');
// 当前请求的毫秒数
define('ALEX_START_TIME', microtime(true));
// 当前分配给 php 的内存量
define('ALEX_START_MEM', memory_get_usage());
// 文件后缀
define('EXT', '.php');
// 环境变量的配置前缀
defined('ENV_PREFIX') or define('ENV_PREFIX', 'ALEX_');
// 配置文件目录  => H:\semir\php\www.semirbiz.com\public\Alex\config\
defined('CONF_PATH') || define('CONF_PATH', ROOT_PATH . DS . 'config' . DS);
// 模板文件目录 => H:\semir\php\www.semirbiz.com\public\Alex\templates\
defined('TEMPLATES_PATH') || define('TEMPLATES_PATH', ROOT_PATH . DS . 'templates' . DS);


// 加载环境变量配置文件
if (is_file(ROOT_PATH . DS . '.env')) {

    // 读取配置文件内容
    $envsContent = file_get_contents(ROOT_PATH . DS . '.env');
    $envsArr = explode("\r", $envsContent);

    $envs = [];
    foreach ($envsArr as $v) {
        $formatV = trim($v);
        if (0 === strpos($formatV, '#')) {
            // 如果将注释单独写成一行，则跳过本次循环
            continue;
        }
        if (strpos($formatV, '#') > 0) {
            // 如果将注释写到配置项的最后时
            $formatV = trim(substr($formatV, 0, strpos($formatV, '#')));
        }
        if (!empty($formatV)) {
            $envs[] = $formatV;
        }
    }

    foreach ($envs as $key => $val) {
        $settingArr = explode('=', $val);
        if (isset($settingArr[0]) && isset($settingArr[1])) {
            $name = ENV_PREFIX . strtoupper($settingArr[0]);  // 环境变量的 key 全部转换成大写
            $setting = $name . '=' . $settingArr[1];
            putenv($setting);  // 将配置信息写入环境变量中
        }
    }

}
