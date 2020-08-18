<?php


namespace Sugaryesp\Library;


class VarDumper
{

    public static function dump($var)
    {
        if (is_array($var)) {
            echo "<br><br> The format data is <br><br>";
            foreach ($var as $k => $v) {
                echo "This is the key => <br>";
                var_dump($k);
                echo "<br> This is the value => <br>";
                var_dump($v);
                echo "<br><br>";
            }

            echo "<br><br> The origin data is <br><br>";
            var_dump($var);

        } else {
            var_dump($var);
        }

        echo "<br><========================================><br>";
    }

}