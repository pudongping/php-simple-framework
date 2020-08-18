<?php

use Sugaryesp\Library\VarDumper;
use Sugaryesp\Library\Env;
use Sugaryesp\Library\Request;
use Sugaryesp\Library\Factory;
use Sugaryesp\Library\Config;

if (!function_exists('dd')) {
    /**
     * 打印调试信息
     *
     * @param mixed ...$vars
     */
    function dd(...$vars)
    {
        foreach ($vars as $v) {
            VarDumper::dump($v);
        }
        exit(1);
    }
}

if (!function_exists('env')) {
    /**
     * 获取环境变量的值
     *
     * @param $key
     * @param null $default
     * @return array|bool|string|\Sugaryesp\Library\没有设置相应的环境变量时定义的默认值|void
     */
    function env($key, $default = null)
    {
        return Env::get($key, $default);
    }
}

if (!function_exists('request_resource_type')) {
    /**
     * 当前资源请求类型
     *
     * @return mixed|string
     */
    function request_resource_type()
    {
        $request = new Request;

        return $request->ResourceType();
    }
}

if (!function_exists('uncamelize')) {
    /**
     * 驼峰命名转下划线命名
     * 思路：
     * 小写和大写紧挨一起的地方,加上分隔符,然后全部转小写
     *
     * @param $camelCaps
     * @param string $separator
     * @return string
     */
    function uncamelize($camelCaps,$separator='_')
    {
        return strtolower(preg_replace('/([a-z])([A-Z])/', "$1" . $separator . "$2", $camelCaps));
    }
}

if (!function_exists('camelize')) {
    /**
     * 下划线转小驼峰
     * 思路：
     * step1.原字符串转小写,原字符串中的分隔符用空格替换,在字符串开头加上分隔符
     * step2.将字符串中每个单词的首字母转换为大写,再去空格,去字符串首部附加的分隔符.
     *
     * @param $uncamelized_words
     * @param string $separator
     * @return string
     */
    function camelize($uncamelized_words,$separator='_')
    {
        $uncamelized_words = $separator. str_replace($separator, " ", strtolower($uncamelized_words));
        return ltrim(str_replace(" ", "", ucwords($uncamelized_words)), $separator );
    }
}

if (!function_exists('model')) {
    /**
     * 获取模型单例实例对象
     *
     * @param $modelName  模型名称
     * @return mixed|null
     */
    function model($modelName)
    {
        return Factory::getModel($modelName);
    }
}

if (!function_exists('config')) {
    /**
     * 以 点号 「.」 的形式访问配置文件中的内容
     *
     * @param null $dotStrKey  带点的字符串 eg => 'database.master.aa.bb.cc.dd'
     * @param null $default  配置默认值
     * @return array|mixed|void|null
     */
    function config($dotStrKey = null, $default = null)
    {
        return Config::getDotConf($dotStrKey, $default);
    }
}