<?php


namespace Sugaryesp\Library;

use Sugaryesp\App;
use Sugaryesp\Library\Database\Database;

class Factory
{

    /**
     * 创建 app 实例对象
     *
     * @return App
     */
    public static function createApp()
    {
        $app = Register::get('app');
        if (!$app) {
            $app = App::getInstance();  // app 对象实例
            Register::set('app', $app);
        }
        return $app;
    }

    /**
     * 获取数据库连接对象
     *
     * @return mixed|Database\MySQLi|null
     */
    public static function getDatabase()
    {
        // 加载数据库配置项
        $databaseConfigs = Config::get('database');
        $currentDBConf = $databaseConfigs['master'];  // 加载指定配置项
        $db = Register::get('db');
        if (!$db) {
            $db = Database::createDatabase($currentDBConf);
            Register::set('db', $db);
        }
        return $db;
    }

    /**
     * 获取模型实例对象
     *
     * @param $modelName  模型名称
     * @return mixed|null
     */
    public static function getModel($modelName)
    {
        $key = 'app_model_' . $modelName;
        $model = Register::get($key);
        if (!$model) {
            $class = '\\App\\Model\\' . ucwords($modelName);
            $model = new $class;
            Register::set($key, $model);
        }
        return $model;
    }

    /**
     * 获取配置文件中的配置
     *
     * @param $configName
     * @return array|mixed|null
     */
    public static function getConfig($configName)
    {
        $key = 'app_config_' . $configName;
        $config = Register::get($key);
        if (!$config) {
            $config = Config::get($configName);
            Register::set($key, $config);
        }
        return $config;
    }


}