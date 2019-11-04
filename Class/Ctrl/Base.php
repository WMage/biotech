<?php

/**
 * Created by PhpStorm.
 * User: White Mage
 * Date: 2019.11.04.
 * Time: 6:18
 */
class CtrlBase extends Singleton {
    /**
     * @var Validator
     */
    protected static $VAL;
    protected $requiredFields = array();

    protected function Load() {
        self::$VAL = Validator::getInstance();
    }

    public function checkFields() {
        $errors = array();
        foreach ($this->requiredFields as $field) {
            if (!isset($_POST[$field]) || empty($_POST[$field])) {
                $errors[] = ("$field required");
            } else {
                $this->autoValidateField($field, $_POST, $arrays);
            }
            unset($_POST[$field]);
        }
        return $errors;
    }

    protected function autoValidateField($field, $array, &$errors) {
        $validators = Validator::VALIDATORS;
        if (isset($validators[$field])) {
            try {
                self::$VAL->$validators[$field]($array[$field]);
                return true;
            } catch (Exception $e) {
                if (trim($array[$field]) == "") {
                    return true;
                }
                $errors[] = ($e->getMessage() . " ($field)");
                return false;
            }
        }
        return true;
    }
}