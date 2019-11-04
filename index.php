<?php
/**
 * Created by PhpStorm.
 * User: White Mage
 * Date: 2019.11.03.
 * Time: 10:57
 */

error_reporting(-1);
include "include/headCommands.php";

if (isset($_GET["prod"])) {
    include "tpl/single.php";
} else {
    include "tpl/manage.php";
}
