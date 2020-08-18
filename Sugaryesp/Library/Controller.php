<?php

namespace Sugaryesp\Library;

abstract class Controller
{

    /**
     * 用于保存传递给模板文件的变量
     *
     * @var array
     */
    protected $data = [];

    /**
     * 用于保存控制器的名称
     *
     * @var
     */
    protected $controllerName;

    /**
     * 用于保存控制器方法的名称
     *
     * @var
     */
    protected $viewName;

    public function __construct($controllerName, $viewName)
    {
        $this->controllerName = $controllerName;
        $this->viewName = $viewName;
    }

    /**
     * 传递给模板文件的变量
     *
     * @param $key
     * @param $value
     */
    public function assign($key, $value)
    {
        $this->data[$key] = $value;
    }

    /**
     * 加载视图文件
     *
     * @param string $viewFilePath  视图文件的路径 eg => 'home/index'
     */
    public function display($viewFilePath = '')
    {
        if (empty($viewFilePath)) {  // 如果不指定模板文件，则使用 「类名称/控制器方法」 这样的路径作为模板路径
            $viewFilePath = strtolower($this->controllerName) . DS . $this->viewName;  // eg => 'home\index'
        }
        $abPath = TEMPLATES_PATH . $viewFilePath . EXT;  // 模板文件的绝对路径地址 eg => 'H:\semir\php\www.semirbiz.com\public\Alex\templates\home\index.php'
        extract($this->data);  // 将数组的键作为变量 数组的值作为变量的值
        if (!is_file($abPath) || !file_exists($abPath)) {
            throw new \Exception("The templates file is not exists =====> " . $abPath);
            die;
        }
        include $abPath;  // 包含相对应的模板文件
    }


}