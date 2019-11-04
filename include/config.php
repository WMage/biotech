<?php
/**
 * Created by PhpStorm.
 * User: White Mage
 * Date: 2018.08.13.
 * Time: 18:59
 */

if (!@include "config/conf." . $_SERVER['SERVER_NAME'] . ".php") {
    include "config/conf.default.php";
}

define('TB_PRODUCT', 'product');
define('TB_PRODUCT_TRANSLATE', 'product_trs');
define('TB_LABEL', 'label');
define('TB_PRODUCT_LABEL', 'product_label');

Settings::set("langs", array(
    1 => "hu",
    2 => "en"
));

define("DEFAULT_LANG", 1);