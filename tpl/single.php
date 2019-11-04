<?php
/**
 * Created by PhpStorm.
 * User: White Mage
 * Date: 2019.11.04.
 * Time: 2:15
 */
/** @var CtrlManage $ctrl */
$ctrl = CtrlManage::getInstance();
$action = "create";
$prodData = array();
if (is_numeric($_GET["prod"])) {//edit
    $prodData = $ctrl->getProduct($_GET["prod"]);
    $action = "edit";
}
if(isset($_POST["action"]))
{
    if($_POST["action"]=="create"){
        $ctrl->newProduct($_POST);
    }
    if($_POST["action"]=="create"){
        $ctrl->updateProduct($_POST);
    }
}
?>
<form method='post' enctype="multipart/form-data">
    <input type="hidden" name="action" value="<?= $action ?>">
    <input type="hidden" name="id" value="<?= @$prodData[0]["ID"] ?>">
    <table>
        <tr>
            <th><?= _l("ID") ?></th>
            <th><?= _l("picture") ?></th>
            <th><?= _l("visibility_start") ?></th>
            <th><?= _l("visibility_end") ?></th>
            <th><?= _l("price") ?></th>
        </tr>
        <tr>
            <td><?= @$prodData[0]["ID"] ?></td>
            <td><input name="picture" title="" type="file"><img
                    src="data:image/jpeg;base64, <?= base64_decode(@$prodData[0]["picture"]) ?>"></td>
            <td><input name="visibility_start" title="" type="date" value="<?= @$prodData[0]["visibility_start"] ?>">
            </td>
            <td><input name="visibility_end" title="" type="date" value="<?= @$prodData[0]["visibility_end"] ?>"></td>
            <td><input name="price" title="" type="number" value="<?= @$prodData[0]["price"] ?>"></td>
        </tr>
    </table>
    <table>
        <tr>
            <th><?= _l("lang") ?></th>
            <th><?= _l("title") ?></th>
            <th><?= _l("desc") ?></th>
            <th><?= _l("labels") ?></th>
        </tr>
        <?php
        /** @var Arrays $arr */
        $arr = Arrays::getInstance();
        foreach (Settings::get("langs") as $code => $lang) {
            $data = $arr->filter_by_value($prodData, array("ID_lang" => $code), false, 1, true);
            ?>
            <tr>
                <td><?= _l($lang) ?></td>
                <td><input name="title_<?= $code ?>" title="" type="text" value="<?= @$data["title"] ?>"></td>
                <td><input name="desc_<?= $code ?>" title="" type="text" value="<?= @$data["desc"] ?>"></td>
                <td>
                    <!--<input name="title_<?/*=$lang*/ ?>" title="" type="date" value="<?/*= @$data["title"] */ ?>">--></td>
            </tr>
            <?php
        }
        ?>
    </table>
    <input type="submit" title="Save">
</form>
