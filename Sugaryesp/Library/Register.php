<?php

namespace Sugaryesp\Library;

class Register
{

    protected static $objects = [];

    /**
     * 注册实例对象
     *
     * @param $alias  对象别名
     * @param $object  实例对象
     */
    public static function set($alias, $object)
    {
        self::$objects[$alias] = $object;
    }

    /**
     * 获取对象实例
     *
     * @param $alias
     * @return mixed|null
     */
    public static function get($alias)
    {
        return isset(self::$objects[$alias]) ? self::$objects[$alias] : null;
    }

    /**
     * 删除对象实例
     *
     * @param $alias
     */
    public static function _unset($alias)
    {
        if (isset(self::$objects[$alias])) {
            unset(self::$objects[$alias]);
        }
    }

}