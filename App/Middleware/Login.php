<?php

namespace App\Middleware;

class Login implements MiddlewareDecorator
{

    public function beforeRequest($controller)
    {
        echo "<br><br>" . get_class($controller) . " login beforeRequest  => <br><br>";
    }

    public function afterRequest($returnValue)
    {
        echo "<br><br> 我是 login  afterRequest<br><br>";
    }

}