<?php
/**
 * Created by PhpStorm.
 * User: White Mage
 * Date: 2019.11.03.
 * Time: 17:08
 */

/** @var CtrlManage $ctrl */
$ctrl = CtrlManage::getInstance();
if (isset($_GET["delete"]) && is_numeric($_GET["delete"])) {
    $ctrl->delProduct($_GET["delete"]);
}
$prods = $ctrl->getProduct();
foreach ($prods as $k => $prod) {
    $prods[$k]["ID"] = "<a href=/?prod=" . $prod["ID"] . ">" . $prod["ID"] . "</a>";
    $prods[$k]["DEL"] = "<a href=/?delete=" . $prod["ID"] . "> X </a>";
    $prods[$k]["picture"] = "<img width='400px'
                    src='data:image/jpeg;base64, " . (@$prod["picture"]) . "'>";
}
echo "<a href=/?prod=new>" . _l("new") . "</a>";
printTable($prods);