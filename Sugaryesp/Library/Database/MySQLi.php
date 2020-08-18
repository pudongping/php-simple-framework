<?php


namespace Sugaryesp\Library\Database;

class MySQLi implements IDatabase
{

    protected $conn;

    public function connect($host, $user, $passwd, $dbname)
    {
        $mysqli = new \mysqli($host, $user, $passwd, $dbname);
//        $mysqli = mysqli_connect($host, $user, $passwd, $dbname);  //  面向过程化风格
        $this->conn = $mysqli;
    }

    public function query($sql)
    {
        return $this->conn->query($sql);
//        return mysqli_query($this->conn, $sql);  //  面向过程化风格
    }

    public function close()
    {
        $this->conn->close();
//        mysqli_close($this->conn);  // 面向过程化风格
    }

    public function fetchCurrentConn()
    {
        return $this->conn;
    }

}