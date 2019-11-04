<?php

/**
 * Created by PhpStorm.
 * User: White Mage
 * Date: 2018.08.17.
 * Time: 22:38
 */
class Validator extends Singleton {
    const VALIDATORS = array(
        'visibility_start' => 'isDate',
        'visibility_end' => 'isDate',
        'price' => 'validNum'
    );

    public function isDate($text) {
        $text = str_replace(array(' ', '.', '-'), '', $text);
        if (strlen($text) != 8) {
            throw new Exception("Invalid date format");
        }
        $y = substr($text, 0, 4);
        $m = substr($text, 4, 2);
        $d = substr($text, 6, 2);
        if (($m > 12) || ($d > 31) || ($y < 1900)) {
            throw new Exception("Invalid date time");
        }
        return true;
    }

    public function validText($text, $regExp = '/^[\p{L}.\', ]{1,50}$/iu') {
        if (!preg_match($regExp, $text)) {
            throw new Exception('A szöveg nem megengedett karaktereket tartalmaz!');
        }
        return true;
    }

    public function validPass($pass, $minlen = 6, $maxlen = 16) {
        if (!preg_match('/\A(?=[-_a-zA-Z0-9]*?[A-Z])(?=[-_a-zA-Z0-9]*?[a-z])(?=[-_a-zA-Z0-9]*?[0-9])[-_a-zA-Z0-9]{' .
            $minlen . ',' . $maxlen . '}\z/', $pass)
        ) {
            throw new Exception('Helytelen jelszó formátum! A jelszzónak tartalmaznia kell kis és nagybetűt plusz számot is. Minimum 6 maximum 16 karakter.');
        }
        return true;
    }

    public function validEmail($email) {
        if (!preg_match('/^[a-zA-Z0-9_\.\-]+\@([a-zA-Z0-9\-]+\.)+[a-zA-Z0-9]{2,4}$/', $email)) {
            throw new Exception('Helytelen e-mail cím formátum!');
        }
        return true;
    }

    public function validURL($url) {
        if (!preg_match('/^(([\w]+:)?\/\/)?(([\d\w]|%[a-fA-f\d]{2,2})+(:([\d\w]|%[a-fA-f\d]{2,2})+)?@)?([\d\w][-\d\w]{0,253}[\d\w]\.)+[\w]{2,4}(:[\d]+)?(\/([-+_~.\d\w]|%[a-fA-f\d]{2,2})*)*(\?(&amp;?([-+_~.\d\w]|%[a-fA-f\d]{2,2})=?)*)?(#([-+_~.\d\w]|%[a-fA-f\d]{2,2})*)?$/',
            $url)
        ) {
            throw new Exception('Helytelen URL cím!');
        }
        return true;
    }

    public function dateyyyymmdd($date) {
        if (!preg_match('/^[0-9]{4}-(0[0-9]|1[0-2])-(0[0-9]|[0-2][0-9]|3[0-1])$/', $date)) {
            throw new Exception('Hibás évszám formátum: 1987-01-21');
        }
        return true;
    }

    public function validNum($num) {
        if (!preg_match('/[0-9]{1,}$/', $num)) {
            throw new Exception('Not number!');
        }
        return true;
    }
}

?>