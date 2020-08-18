<?php


namespace App\Middleware;


class Json implements MiddlewareDecorator
{

    public function beforeRequest($controller)
    {
    }

    public function afterRequest($returnValue)
    {
        $type = request_resource_type();
        if ('json' === $type) {
            echo json_encode($returnValue, JSON_UNESCAPED_UNICODE);
        }
    }


}