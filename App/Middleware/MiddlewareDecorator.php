<?php
/**
 * 定义应用中间件装饰器接口
 *
 * Created by PhpStorm.
 * User: Alex
 * Date: 2020-8-13
 * Time: 16:04
 */

namespace App\Middleware;


interface MiddlewareDecorator
{
    /**
     * 请求前
     *
     * @param $controller
     * @return mixed
     */
    public function beforeRequest($controller);

    /**
     * 请求后
     *
     * @param $returnValue
     * @return mixed
     */
    public function afterRequest($returnValue);
}