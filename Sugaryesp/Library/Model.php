<?php
/**
 * 模型抽象类
 */

namespace Sugaryesp\Library;

abstract class Model extends DB
{

    /**
     * @var int 当前数据的 id
     */
    protected $id;

    /**
     * @var 数据库连接对象
     */
    protected $db;

    /**
     * @var 当前模型所对应的数据表 （下划线表名）
     */
    protected $table;

    /**
     * @var bool 是否需要自动写入时间戳
     */
    protected $autoWriteTimestamp = true;

    /**
     * @var string 创建时间字段
     */
    protected $createTime = 'created_at';

    /**
     * @var string 更新时间字段
     */
    protected $updateTime = 'updated_at';

    /**
     * 允许操作的数据库字段列表
     *
     * @var array
     */
    protected $fillable = [];

    /**
     * @var array 对象方式进行插入或者更新时，调用 save 方法临时保存的原始数据
     */
    protected $save_data = [];

    /**
     * 当前 id 所对应的数据值
     *
     * @var array|mixed|当前需要执行的sql语句
     */
    protected $data = [];

    // 记录观察者类
    private $observers = [];

    public function __construct($id = 0)
    {
        $this->id = intval($id);
        $this->init();
        if (!empty($id)) {
            $this->data = $this->find($id);
        }
    }

    /**
     * 当实例化对象时传入 id 时，可以获取当前数据的 id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * 初始化模型时需要调用的方法
     */
    protected function init()
    {
        $this->db = Factory::getDatabase();  // 获取数据库连接对象 （单例对象）
        $this->table = $this->getTableName();  // 下划线数据表名称
        $this->addObserver();  // 初始化观察者
    }

    /**
     * 添加观察者实例
     */
    protected function addObserver()
    {
        // 加载 model 相关的配置文件
        $modelConfigs = config('model');

        // 保存需要调用的 观察者 实例
        $configObservers = [];
        // 如果有全局所需要调用的观察者时候
        if (isset($modelConfigs['global_observer'])) {
            $configObservers = array_merge($configObservers, $modelConfigs['global_observer']);
        }
        // 特定模型所需要调用的观察者
        if (isset($configObservers[$this->table]['observer'])) {
            $configObservers = array_merge($configObservers, $configObservers[$this->table]['observer']);
        }

        // 实例化后的观察者数组
        $observerObjs = [];
        foreach ($configObservers as $configObserver) {
            $observerObjs[] = new $configObserver;
        }

        $this->observers = $observerObjs;
    }

    /**
     * 调用观察者
     *
     * @param $event
     */
    public function notify($event)
    {
        foreach ($this->observers as $observer) {
            $observer->event($event);
        }
    }

    /**
     * 获取当前模型所对应的表名
     *
     * @return string  当前模型所对应的表名 （下划线命名）
     */
    public function getTableName()
    {
        if ($this->table) return $this->table;
        $tableName = uncamelize(str_replace('App\\Model\\', '', get_class($this)));  // 下划线命名
        return $tableName;
    }

    /**
     * 是否需要自动写入时间字段
     *
     * @param bool $isAuto
     * @return $this
     */
    public function isAutoWriteTimestamp($isAuto = true)
    {
        $this->autoWriteTimestamp = $isAuto;
        return $this;
    }

    /**
     * 新建数据
     *
     * @param array $input
     * @return array|mixed|当前需要执行的sql语句|插入成功后的记录
     * @throws \Exception
     */
    public function create($input = [])
    {
        if (empty($input)) {
            throw new \Exception('The input data not be allowed to empty !');
        }
        // 自动写入时间字段
        if ($this->autoWriteTimestamp) {
            $input = array_merge($input, [
                $this->createTime => date('Y-m-d H:i:s'),
                $this->updateTime => date('Y-m-d H:i:s'),
            ]);
        }
        // 调用 DB 父类中的 insert 方法
        return $this->insert($input);
    }

    /**
     * 更新语句
     *
     * @param array $input
     * @return array|mixed|当前需要执行的sql语句|返回影响的行数
     * @throws \Exception
     */
    public function modify($input = [])
    {
        if (empty($input)) {
            throw new \Exception('The input data not be allowed to empty !');
        }
        if ($this->autoWriteTimestamp) {
            $input = array_merge($input, [$this->updateTime => date('Y-m-d H:i:s')]);
        }
        return $this->fetchSql()->update($input);
    }

    public function __get($field)
    {
        if (isset($this->data[$field])) {
            return $this->data[$field];
        }
    }

    public function __set($field, $value)
    {
        $this->data[$field] = $value;
    }

    /**
     * 过滤指定字段进行数据库操作
     *
     * @param array $input
     * @return array|mixed
     */
    public function fill($input = [])
    {
        if (empty($input)) return [];
        if (empty($this->fillable)) return [];
        foreach ($input as $field => $value) {
           if (!in_array($field, $this->fillable)) {
               unset($input[$field]);
           }
        }
        // 进行过滤字段后的数据
        $this->save_data = $input;
    }

    /**
     * 对象的方式更新或者创建数据时
     *
     * @return array|mixed|当前需要执行的sql语句|插入成功后的记录|返回影响的行数
     * @throws \Exception
     */
    public function save()
    {
        if (!empty($this->data)) {
            // 如果直接采用对象设置属性的方式来进行 更新或者创建 数据，那么自动调用 fill 方法
            $this->fill($this->data);
        }

        if (empty($this->save_data)) {
            throw new \Exception('The input data not be allowed to empty !');
        }
        // 通过判断主键 id 是否在变动数据中，来判断是更新操作还是添加操作
        if (in_array($this->primary_key, array_keys($this->save_data))) {
            // 更新操作
            $result = $this->where($this->primary_key, $this->save_data[$this->primary_key])->modify($this->save_data);
        } else {
            $result = $this->create($this->save_data);
        }

        return $result;
    }

}