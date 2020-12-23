<?php

namespace Sugaryesp\Library;

class DB
{

    /**
     * @var 当前数据库的表名
     */
    protected $table;

    /**
     * 当前数据库连接对象实例
     *
     * @var mixed|Database\Database\MySQLi|null
     */
    protected $db;

    /**
     * @var 当前需要执行的sql语句
     */
    protected $sql;

    protected $insert_sql;
    protected $update_sql;
    protected $group_sql;
    protected $order_sql;
    protected $limit_sql;

    /**
     * @var 指定查询的字段
     */
    public $fields = ' * ';

    /**
     * where 条件数组
     *
     * @var string[]
     */
    public $where = ['1=1'];

    /**
     * @var or_where 条件
     */
    protected $or_where;

    /**
     * @var 当前是 CRUD 中的哪一种查询 insert|select|update|delete
     */
    protected $action;

    /**
     * 是否开启获取 sql
     *
     * @var bool
     */
    protected $is_fetch_sql = false;

    /**
     * @var string 默认主键为 id
     */
    protected $primary_key = 'id';

    public function __construct($table)
    {
        $this->table = $table;
        $this->db = Factory::getDatabase();
    }

    /**
     * 实例化数据表对象
     *
     * @param string $table  数据库的表名
     * @return DB  自身实例
     */
    public static function table($table = '')
    {
        return new self($table);
    }

    /**
     * 指定字段查询
     *
     * @param null $fields  需要被查询的字段，支持 'id,name,age' 或者 ['id', 'name', 'age']
     * @return $this
     */
    public function fields($fields = null)
    {
        if (is_null($fields)) {  // 当没有设置字段时
            $this->fields = ' * ';  // 默认为 * 所有
        }

        if (is_string($fields)) {  // 当设置的字段为字符串时
            $this->fields = $fields;
        }

        if (is_array($fields)) {  // 当设置的字段为数组时
            $this->fields = implode(',', $fields);
        }

        return $this;
    }

    /**
     * 拼接 where 查询语句
     *
     * @param $field  字段名
     * @param $exp  表达式
     * @param $value  字段值
     * @return $this
     */
    public function where($field = null, $exp = null, $value = null)
    {
        if (is_null($field) || is_null($exp)) {
            throw new \Exception('Mysqli ERR: => The field must have !');
        }
        if (is_null($value)) {
            // 当不传第三个参数的时候，默认第二个参数为所需要查询的值，此时的表达式为 =
            $sql = '`' . trim($field) . '`' . ' = "' . trim($exp) . '"';
        } else {
            // 当三个参数都具备时
            $sql = '`' . trim($field) . '`' . ' ' . $exp . '"' . trim($value) . '"';
        }
        $this->where = array_merge($this->where, [$sql]);
        return $this;
    }

    /**
     * 拼接 or where 条件语句
     *
     * @param null $field  字段名
     * @param null $exp  表达式
     * @param null $value  字段值
     * @return $this
     * @throws \Exception
     */
    public function orWhere($field = null, $exp = null, $value = null)
    {
        if (is_null($field) || is_null($exp)) {
            throw new \Exception('Mysqli ERR: => The field must have !');
        }

        if (is_null($value)) {
            // 当不传第三个参数的时候，默认第二个参数为所需要查询的值，此时的表达式为 =
            $sql = '`' . trim($field) . '`' . ' = "' . trim($exp) . '"';
        } else {
            // 当三个参数都具备时
            $sql = '`' . trim($field) . '`' . ' ' . $exp . ' "' . trim($value) . '" ';
        }

        $orWhereSql = '';
        if (!$this->or_where) {
            // 当此时并没有 orWhere 条件语句时
            $orWhereSql = ' and 1=1 or ' . $sql;
        } else {
            $orWhereSql = $this->or_where;
            $orWhereSql .= ' or ' . $sql;
        }

        $this->or_where = $orWhereSql;

        return $this;
    }

    /**
     * 拼接 where in 查询语句
     *
     * @param $field 字段名
     * @param $value 查询范围，支持 '1,2,3' 或者 [1,2,3]
     * @return $this
     */
    public function whereIn($field, $value)
    {
        $valueStr = '';
        if (is_string($value)) {
            $valueStr = ' ( ' . $value . ' ) ';
        }
        if (is_array($value)) {
            $valueStr = ' ( ' . implode(',', $value) . ' ) ';
        }
        $sql = '`' . trim($field) . '` in ' . $valueStr;
        $this->where = array_merge($this->where, [$sql]);
        return $this;
    }

    /**
     * 拼接 where not in 查询语句
     *
     * @param $field  字段名
     * @param $value  查询范围，支持 '1,2,3' 或者 [1,2,3]
     * @return $this
     */
    public function whereNotIn($field, $value)
    {
        $valueStr = '';
        if (is_string($value)) {
            $valueStr = ' ( ' . $value . ' ) ';
        }
        if (is_array($value)) {
            $valueStr = ' ( ' . implode(',', $value) . ' ) ';
        }
        $sql = '`' . trim($field) . '` not in ' . $valueStr;
        $this->where = array_merge($this->where, [$sql]);
        return $this;
    }

    /**
     * 分组查询语句
     *
     * @param null $field 支持 'id,name,age' 或者单字段的链式语法
     * @return $this
     * @throws \Exception
     */
    public function group($field = null)
    {
        if (is_null($field)) {
            throw new \Exception('Mysqli ERR: => the group method must have field');
        }
        $groupSql = '';
        if (!$this->group_sql) {
            // 当分组 group_sql 为 null 时
            $groupSql = ' group by ' . $field;
        } else {
            $groupSql = $this->group_sql;
            $groupSql .= ',' . $field;
        }

        $this->group_sql = $groupSql;

        return $this;
    }

    /**
     * 排序语句
     *
     * @param null $field  字段名
     * @param string $sort  排序规则
     * @return $this
     * @throws \Exception
     */
    public function order($field = null, $sort = 'desc')
    {
        if (is_null($field)) {
            throw new \Exception('Mysqli ERR: => the order method must have one args');
        }

        $orderSql = '';
        if (!$this->order_sql) {
            // 当排序 order_sql 为 null 时，
            $orderSql = ' order by ' . $field . ' ' . $sort;
        } else {
            $orderSql = $this->order_sql;
            $orderSql .= ' , ' . $field . ' ' . $sort;
        }

        $this->order_sql = $orderSql;

        return $this;
    }

    /**
     * limit 语句
     *
     * @param null $offset  只有一个参数的时候，为显示多少条数，两个参数的时候，为偏移量
     * @param null $limit  当有此参数的时候，表示查询数据的条数
     * @return $this
     */
    public function limit($offset = null, $limit = null)
    {
        $sql = '';
        if (is_null($offset) && is_null($limit)) {
            // 当不传参数时，默认取 20 条数据
            $sql = ' limit ' . config('app.default_limit');
        }
        if (!is_null($offset) && is_null($limit)) {
            // 当只传第一个参数时，默认第一个参数为需要查询的数据条数
            $sql = ' limit ' . intval($offset);
        }
        if (!is_null($offset) && !is_null($limit)) {
            // 表示：从第 $offset 开始的 $limit 行
            $sql = ' limit ' . $limit . ' offset ' . $offset;
//            $sql = ' limit ' . $offset . ',' . $limit;  // 或者这样
        }

        $this->limit_sql = $sql;

        return $this;
    }

    /**
     * 编写原生态的 sql 语句
     *
     * @param $sql  sql 语句
     * @return $this
     */
    public function draw($sql)
    {
        $this->sql = $sql;
        return $this;
    }

    /**
     * 获取当前需要执行的 sql 语句
     *
     * @return mixed
     */
    public function fetchSql()
    {
        $this->is_fetch_sql = true;  // 开启只打印 sql 语句
        return $this;
    }

    /**
     * 预拼接 sql 语句
     *
     * @return $this
     */
    protected function prepareSql()
    {
        $whereStrSql = '';
        // 如果有 where 语句时
        if ($this->where) {
            foreach ($this->where as $where) {
                $whereStrSql .= ' and ' . $where;
            }
            $whereStrSql = str_replace(' and 1=1 and', ' where ', $whereStrSql);
        }

        // 如果此时有 or_where 条件语句时
        if ($this->or_where) {
            $orWhereSql = str_replace(' and 1=1 or', ' ', $this->or_where);
            // 此时的 where 语句为空时，则表示只有 orwhere 语句
            if ('and 1=1' === trim($whereStrSql)) {
                $whereStrSql = ' where ( ' . $orWhereSql . ' ) ';
            } else {
                $whereStrSql .= ' and ( ' . $orWhereSql . ' ) ';
            }
        }

        switch ($this->action) {
            case 'insert':
                // 拼接 insert 语句
                $this->sql = 'insert into ' . $this->table . $this->insert_sql;
                break;
            case 'delete':
                // 拼接 delete 语句
                $this->sql = ' delete from ' . $this->table . $whereStrSql;
                break;
            case 'update':
                // 拼接 update 语句
                $this->sql = ' update ' . $this->table . ' set ' . $this->update_sql . $whereStrSql;
                break;
            case 'select':
                // 拼接 select 查询语句
                if ('and 1=1' === trim($whereStrSql)) {  // 此时表示没有查询条件时
                    $whereStrSql = null;
                }
                $this->sql = ' select ' . $this->fields . ' from ' . $this->table . $whereStrSql . $this->group_sql . $this->order_sql . $this->limit_sql;
                break;
        }

        return $this;
    }

    /**
     * 执行 sql 语句
     *
     * @param false $isSelect  原生查询的时候有用 ，当前查询是否为 select 查询
     * @return array|当前需要执行的sql语句
     * @throws \Exception
     */
    public function execute($isSelect = false)
    {
        if ($this->is_fetch_sql) {
            // 如果开启只获取 sql ，那么只返回 sql 语句
            return $this->sql;
        }

        $result = $this->db->query($this->sql);
        if (false === $result) {
            throw new \Exception('数据库操作失败！');
        }

        if ($isSelect) {  // 当执行 draw 方法原生查询的时候， select 查询的返回结果需要过滤，因此可以设置 $isSelect = true 来实现返回结果
            $rows = [];
            while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
                $rows[] = $row;
            }
            return $rows;
        }

        return $result;
    }

    /**
     * 添加语句
     *
     * @param array $data
     * @return mixed| 插入成功后的记录 id
     * @throws \Exception
     */
    public function insert($data = [])
    {
        $this->action = 'insert';
        if (empty($data)) {
            throw new \Exception('缺乏字段');
        }

        // 字段
        $fields = implode(',', array_map(function ($f) {
            return '`' . $f . '`';
        }, array_keys($data)));

        // 字段对应的值
        $values = implode(',', array_map(function ($v) {
            return '"' . trim($v) . '"';
        }, array_values($data)));

        $this->insert_sql = ' ( ' . $fields . ' ) values ( ' . $values . ' ) ';

        $this->prepareSql();

        $result = $this->execute();  // 执行 sql 语句
        if ($this->is_fetch_sql) return $result;  // 如果只是打印 sql 则直接返回

        return $this->insertGetId();
    }

    /**
     * 删除语句
     * 当直接执行 delete 语句时，会报错，因为 where 语句默认错误（防止误删除）
     *
     * @param null $id 主键 id, int|array
     * @return mixed|返回影响的行数
     * @throws \Exception
     */
    public function delete($id = null)
    {
        $this->action = 'delete';

        if ($id) {
            if (is_int($id)) {
                $this->where($this->primary_key, '=', $id);
            }
            if (is_array($id)) {
                $this->whereIn($this->primary_key, $id);
            }
        }

        if (!$this->sql) {
            $this->prepareSql();  // 预备 sql
        }

        $result = $this->execute();  // 执行 sql 语句
        if ($this->is_fetch_sql) return $result;  // 如果只是打印 sql 则直接返回

        return $this->fetchNumRows();
    }

    /**
     * 更新语句
     *
     * @param array $data
     * @return mixed|返回影响的行数
     * @throws \Exception
     */
    public function update($data = [])
    {
        $this->action = 'update';
        if (empty($data)) {
            throw new \Exception('缺乏字段');
        }

        // 更新的 sql 语句，字段和字段值的组合
        $updataSql = '';
        foreach ($data as $k => $v) {
            $updataSql .= '`' . $k . '` = "' . trim($v) . '" ,';
        }
        $updataSql = rtrim($updataSql, ',');

        $this->update_sql = $updataSql;

        $this->prepareSql();  // 预备 sql

        $result = $this->execute();  // 执行 sql 语句
        if ($this->is_fetch_sql) return $result;  // 如果只是打印 sql 则直接返回

        return $this->fetchNumRows();
    }

    /**
     * 查询语句
     *
     * @return array|当前需要执行的sql语句
     * @throws \Exception
     */
    public function select()
    {
        $this->action = 'select';  // 标识此时为 查询语句
        if (!$this->sql) {
            $this->prepareSql();  // 预备 sql
        }
        $result = $this->execute();  // 执行 sql 语句
        if ($this->is_fetch_sql) return $result;  // 如果只是打印 sql 则直接返回
        $rows = [];
        while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
            $rows[] = $row;
        }
        return $rows;
    }

    /**
     * 根据主键查询
     *
     * @param int $id
     * @return array|mixed|当前需要执行的sql语句
     * @throws \Exception
     */
    public function find($id = 0)
    {
        if (!$id) {
            throw new \Exception('the primary key is not allowed empty !');
        }
        $this->where($this->primary_key, $id);
        $results = $this->select();
        if (is_array($results) && !empty($results)) {
            $results = $results[0];  // 有数据时，返回数组中的第一条数据
        }
        return $results;
    }

    /**
     * 获取自定义封装数据库对象
     *
     * @return mixed|Database\Database\MySQLi|null
     */
    public function getCurrentDb()
    {
        return $this->db;
    }

    /**
     * 获取原生态数据库连接对象
     *
     * @return mixed
     */
    public function fetchCurrentConnObj()
    {
        return $this->db->fetchCurrentConn();
    }

    /**
     * 添加数据后如果需要返回新增数据的 id
     *
     * @return mixed
     */
    public function insertGetId()
    {
        return $this->fetchCurrentConnObj()->insert_id;
    }

    /**
     * 返回影响行数
     *
     * @return mixed
     */
    public function fetchNumRows()
    {
        return $this->fetchCurrentConnObj()->affected_rows;
    }


}