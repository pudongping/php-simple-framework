<?php

namespace Sugaryesp\Library\Database;

interface IDatabase
{

    /**
     * 创建数据库连接
     *
     * @param $host  连接地址
     * @param $user  用户名
     * @param $passwd  密码
     * @param $dbname  数据库
     * @return mixed
     */
    public function connect($host, $user, $passwd, $dbname);

    /**
     * 执行数据库
     *
     * @param $sql  需要执行的 sql 语句
     * @return mixed
     */
    public function query($sql);

    /**
     * 获取当前的数据库连接对象
     *
     * @return mixed
     */
    public function fetchCurrentConn();

    /**
     * 关闭数据库连接
     *
     * @return mixed
     */
    public function close();

}