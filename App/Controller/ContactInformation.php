<?php


namespace App\Controller;

use Sugaryesp\Library\Controller;
use Sugaryesp\Library\Factory;
use App\Model\ContactInformation as ContactInformationModel;
use Sugaryesp\Library\DB;

class ContactInformation extends Controller
{

    public function index()
    {
        $name = 'Alex';
        $age = 18;
        return compact('name', 'age');
    }

}