<?php

namespace App\Controller;

use Sugaryesp\Library\Controller;
use Sugaryesp\Library\Config;

class Home extends Controller
{

    public  function index()
    {

        $helloWord = Config::get('controller')['default'];
        $this->assign('helloWord', $helloWord);
        $this->display();

//        $name = 'Alex';
//        $age = 18;
//        return compact('name', 'age');
//        $this->assign('name', 'Alex');
//        $this->display();
    }

}