<?php
/**
 * Created by PhpStorm.
 * User: White Mage
 * Date: 2019.11.03.
 * Time: 20:29
 * @param int|bool $lCode (lang id or false)
 */

function loadLabels($lCode = false) {
    if (!$lCode) {
        $lCode = getCurrentLangCode();
    }
    Settings::set($lCode, include("lang/label/" . getLang($lCode) . ".php"), "lang");
}

function getCurrentLangCode() {
    if (isset($_SESSION["lang"])) {
        return $_SESSION["lang"];
    }
    return 1;
}

function getLang($code = false) {
    if ($code === false) {
        $code = getCurrentLangCode();
    }
    $langs = Settings::get("langs");
    return $langs[$code] ?: $langs[DEFAULT_LANG];
}

function updateCurrentLang($code = false) {
    if ($code === false) {
        if (isset($_GET["lang"])) {
            $code = $_GET["lang"];
        } elseif (isset($_SESSION["lang"])) {
            return;
        }
    }
    $langs = Settings::get("langs");
    $_SESSION["lang"] = isset($langs[$code]) ? $code : DEFAULT_LANG;
}

function _l($text, $lCode = false) {
    if ($lCode === false) $lCode = getCurrentLangCode();
    if (!Settings::get($lCode, "lang")) loadLabels($lCode);
    $labels = Settings::get($lCode, "lang");
    return isset($labels[$text]) ? $labels[$text] : $text;
}

function printTable($array) {
    if (empty($array)) return;
    $headers = array_keys($array[0]);
    echo "<table>";
    echo "<tr>";
    foreach ($headers as $header) {
        echo "<th>" . _l($header) . "</th>";
    }
    echo "</tr>";
    foreach ($array as $line) {
        echo "<tr>";
        foreach ($line as $field) {
            echo "<td>$field</td>";
        }
        echo "</tr>";
    }
    echo "</table>";
}