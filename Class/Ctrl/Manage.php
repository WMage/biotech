<?php

/**
 * Created by PhpStorm.
 * User: White Mage
 * Date: 2019.11.03.
 * Time: 7:22
 */
class CtrlManage extends CtrlBase {
    /**
     * @var Mysql
     */
    protected static $DB;
    /**
     * @var Arrays
     */
    protected static $ARRAY;

    protected function Load() {
        parent::Load();
        self::$DB = Mysql::getInstance();
        self::$ARRAY = Arrays::getInstance();
        $this->requiredFields = array(
            'visibility_start',
            'visibility_end',
            'price'
        );
        foreach (Settings::get("langs") as $k => $v) {
            $this->requiredFields[] = 'title_' . $k;
            $this->requiredFields[] = 'desc_' . $k;
        }
    }

    public function getProduct($id = false) {
        return self::$ARRAY->query_result_to_array(
            self::$DB->select(
                array(
                    "p.ID",
                    "p.picture",
                    "p.visibility_start",
                    "p.visibility_end",
                    "p.price",
                    "pt.title",
                    "pt.desc",
                    "pt.ID_lang"
                ),
                TB_PRODUCT . " p",
                $id ? ("p.ID=" . $id) : ("pt.ID_lang=" . getCurrentLangCode()),
                array(),
                "",
                array(array(
                    "p",
                    "ID",
                    TB_PRODUCT_TRANSLATE,
                    "pt",
                    "ID_prod",
                    "LEFT"
                ))
            )
        );
    }

    public function delProduct($id) {
        self::$DB->delete(TB_PRODUCT, "ID=" . $id);
    }

    public function newProduct($data) {
        $errors = $this->checkFields();
        if (!empty($errors)) {
            var_dump($errors);
            return;
        }
        $product_id = self::$DB->insert(
            array(
                "picture" => base64_encode(file_get_contents($_FILES['picture']['tmp_name'])),
                "visibility_start" => $data["visibility_start"],
                "visibility_end" => $data["visibility_end"],
                "price" => $data["price"],
            ),
            TB_PRODUCT);
        foreach (Settings::get("langs") as $k => $v) {
            self::$DB->insert(
                array(
                    "ID_prod" => $product_id,
                    "ID_lang" => $k,
                    "title" => $data["title_" . $k],
                    "desc" => $data["desc_" . $k],
                ),
                TB_PRODUCT_TRANSLATE);
        }
    }

    public function updateProduct($data) {

    }
}