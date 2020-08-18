<?php


namespace Sugaryesp\Library\Database;


class PDO implements IDatabase
{
    protected $conn;

    public function connect($host, $user, $passwd, $dbname)
    {
        $conn = new \PDO("mysql:host=$host;dbname=$dbname", $user, $passwd);
        $this->conn = $conn;
    }

    public function query($sql)
    {
        return $this->conn->query($sql);
    }

    public function fetchCurrentConn()
    {

    }

    public function close()
    {
        unset($this->conn);
    }

}