<?php


namespace App\Middleware;


class Template implements MiddlewareDecorator
{

    /**
     * @var  \Sugaryesp\Library\Controller
     */
    protected $controller;

    public function beforeRequest($controller)
    {
        $this->controller = $controller;
    }

    /**
     * 如果需要输出 html ，则可以直接在类方法中直接 return ['name' => 'alex', 'age' => 18];
     * 不需要单独再去调用 assign() 和 display()
     *
     * @param $returnValue
     * @return mixed|void
     */
    public function afterRequest($returnValue)
    {
        $type = request_resource_type();
        if ('html' === $type) {
            if (!empty($returnValue)) {
                foreach ($returnValue as $k => $v) {
                    $this->controller->assign($k, $v);
                }
            }
            $this->controller->display();
        }
    }

}