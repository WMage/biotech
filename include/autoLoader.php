<?php
/**
 * Created by PhpStorm.
 * User: White Mage
 * Date: 2018.08.13.
 * Time: 18:50
 */

spl_autoload_register('classLoader', true);

function classLoader($name, $dir = "Class") {
    $regExpList = array(
        "[A-Z][^A-Z]",
        "[A-Z][A-Z^]",
        "[A-Z][A-Z^]{2,}"
    );
    foreach ($regExpList as $rExp) {
        $pieces = preg_split('/(?=' . $rExp . ')/', $name, -1, PREG_SPLIT_NO_EMPTY);
        $path = $dir . "/" . implode("/", $pieces) . ".php";
        @include_once($path);
        if (class_exists($name) || interface_exists($name)) {
            return true;
        }
    }
    return false;
}

