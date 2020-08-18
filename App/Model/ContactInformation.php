<?php

namespace App\Model;

use Sugaryesp\Library\Model;

class ContactInformation extends Model
{

    /**
     * 指定变动字段，需要注意的是必须要含有主键字段，否则无法更新
     *
     * @var string[]
     */
    protected $fillable = ['id', 'name', 'age'];

}