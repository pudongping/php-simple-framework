<?php

namespace Sugaryesp\Library\Database;

class Database
{

    /**
     * 创建数据库连接
     * （自定义 mysqli 对象）
     *
     * @param $databaseConfigs  数据库配置信息
     * @return MySQLi  mysqli 实例对象
     */
    public static function createDatabase($databaseConfigs)
    {
        $db = new MySQLi();
        $db->connect($databaseConfigs['host'], $databaseConfigs['username'], $databaseConfigs['password'], $databaseConfigs['database']);
        // 检查连接
        if (mysqli_connect_errno()) {
            printf("Connect failed: %s\n", mysqli_connect_error());
            exit();
        }
        $db->query("SET NAMES {$databaseConfigs['charset']}");  // 设置编码语言类型
        return $db;
    }

}