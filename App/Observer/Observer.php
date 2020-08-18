<?php

namespace App\Observer;

interface Observer
{

    public function event($event = null);

}