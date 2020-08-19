<?php


namespace Sugaryesp\Library;


class Request
{

    /**
     * 当前请求的资源需要以什么格式返回出去
     *
     * @return mixed|string
     */
    public function ResourceType()
    {

        $type = null;

        if (isset($_GET['app'])) {
            $type = $_GET['app'];
        }

        if (isset($_POST['app'])) {
            $type = $_POST['app'];
        }

        return $type;
    }

}