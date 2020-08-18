<?php


namespace Sugaryesp\Library;


class Env
{

    /**
     * 获取环境变量中的值
     *
     * @param $key  环境变量的 key
     * @param $default 没有设置相应的环境变量时定义的默认值
     * @return array|void|bool|string
     */
    public static function get($key, $default)
    {
        $val = getenv(ENV_PREFIX . strtoupper($key));
        if (empty($val)) return $default;  // 当环境变量中没有设置对应的值时，返回默认值

        switch (strtolower($val)) {
            case 'true':
            case '(true)':
                return true;
            case 'false':
            case '(false)':
                return false;
            case 'empty':
            case '(empty)':
                return '';
            case 'null':
            case '(null)':
                return;
            default:
                return $val;
        }

    }

}