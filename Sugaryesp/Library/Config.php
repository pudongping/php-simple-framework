<?php
/**
 * 使用 ArrayAccess 实现配置文件的加载
 *
 * Created by PhpStorm.
 * User: Alex
 * Date: 2020-8-11
 * Time: 17:45
 */

namespace Sugaryesp\Library;

class Config implements \ArrayAccess
{

    /**
     * @var 用于保存存放配置文件的目录
     */
    protected $path;

    /**
     * @var array 以文件名称为 key 保存的配置项
     */
    protected $configs = [];


    public function __construct($path)
    {
        $this->path = $path;
    }

    public function offsetSet($key, $value)
    {
        throw new \Exception('cannot write config file');
    }

    /**
     * 以数组的形式读取对象时
     *
     * @param mixed $key
     * @return mixed
     */
    public function offsetGet($key)
    {
        if (!isset($this->configs[$key]) && empty($this->configs[$key])) {
            // 配置文件的绝对路径 eg => H:\semir\php\www.semirbiz.com\public\Alex/config\database.php
            $configFilePath = $this->path . DS . $key . EXT;
            if (file_exists($configFilePath) && is_file($configFilePath)) {  // 如果存在配置文件
                $config = require $configFilePath;  // 配置文件中的数组内容
                $this->configs[$key] = $config;
            }
        }

        return isset($this->configs[$key]) ? $this->configs[$key] : [];
    }

    /**
     * 检测数组 key 是否存在
     * （当调用 isset() 或者 empty() 函数时，此方法会调用）
     *
     * @param mixed $key
     * @return bool
     */
    public function offsetExists($key)
    {
        return isset($this->configs[$key]);
    }

    /**
     * 删除数组的 key
     * （当调用 unset() 函数时，此方法会调用）
     *
     * @param mixed $key
     */
    public function offsetUnset($key)
    {
        unset($this->configs[$key]);
    }

    /**
     * 获取配置文件中的配置
     *
     * @param $fileName  配置文件的文件名称 eg： database
     * @return array|mixed
     */
    public static function get($fileName)
    {
        $config = new self(CONF_PATH);
        return $config[$fileName];  // 以数组的形式调用对象实例
    }

    /**
     * 以 点号 「.」 的形式访问配置文件中的内容
     *
     * @param null $dotStrKey  带点的字符串 eg => 'database.master.aa.bb.cc.dd'
     * @param null $default  配置默认值
     * @return array|mixed|void|null
     */
    public static function getDotConf($dotStrKey = null, $default = null)
    {
        if (empty($dotStrKey)) return;
        $configFileNum = strpos($dotStrKey, '.');
        if ($configFileNum) {  // 当 key 含有 点号 「.」 时，则会返回起始位置， eg => 8，不含有 点号 时，返回 false
            $configFileName = substr($dotStrKey, 0, $configFileNum);  // eg => database
            // 加载配置文件内容
            $configs = Factory::getConfig($configFileName);
            $childConStr = str_replace($configFileName . '.', '', $dotStrKey);  // eg => master.aa.bb
            $childConArr = explode('.', $childConStr);  // eg => ['master', 'aa', 'bb']
            foreach ($childConArr as $v) {
                $configs = isset($configs[$v]) ? $configs[$v] : $default;
            }
            return $configs;
        }

        $configs = Factory::getConfig($dotStrKey);

        return !empty($configs) ? $configs : $default;
    }

}