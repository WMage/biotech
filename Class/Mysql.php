<?php
/**
 * Created by PhpStorm.
 * User: White Mage
 * Date: 2018.08.13.
 * Time: 19:03
 */

/**
 * class DBMysql
 * @param Host
 * @param User
 * @param Password
 * @param Name
 * Adatbázis kezelő osztály
 */
class Mysql extends Singleton {
    protected $mysqli; //DEBUG mód (SQL hibák látszanak-e)
    protected $last_query; //Utoljára futtatott Query
    private $debug = true; //MySQLi Objektum
    /*---------------------------------------------------------------------------------------------*/

    /**
     * Class Constructor
     * Létrehozza A MySQL kapcsolatot (példányosítás a mysqli osztályból)
     * @param $host
     * @param $user
     * @param $password
     * @param $name
     * @throws Exception
     */
    protected function __construct($host = false, $user = false, $password = false, $name = false) {
        if ($host == false && $user == false && $password == false && $name == false) {
            list($host, $user, $password, $name) = Settings::get('config', 'DB');
        }
        $this->mysqli = @new mysqli($host, $user, $password, $name);
        if ($this->mysqli->connect_errno) {
            print (utf8_decode("Nem sikerült kapcsolódni az adatbázishoz! Hibakód:") . $this->
                    mysqli->connect_errno) . "\n<br />";
            print (utf8_decode("Részletek: ") . $this->mysqli->connect_error);
            exit();
        }
        $this->mysqli->query("SET character_set_results = 'utf8', character_set_client = 'utf8', character_set_connection = 'utf8', character_set_database = 'utf8', character_set_server = 'utf8'");
    }
    /*---------------------------------------------------------------------------------------------*/
    /**
     * @function public select
     * @param string $what mezőnevek (ARRAY vagy STRING)
     * @param string $table tábla neve (STRING)
     * @param string $where Feltétel (STRING)
     * @param mixed $limit limit (MIXED)
     * @param string $order (STRING)
     * @param array $join (ARRAY)
     * @return mysqli_result (Mysqli OBJECT)
     * Lefutatt egy lekérdezést a táblában és a lekérdezés eredménye lesz a visszatérési érték
     * ami egy mysqli Objektum, ennek mezői tartalmazzák az eremdényt. A mezőnevek a tábla mezőnevei.
     * A what paraméterben a mezőneveket lehet megadni ami egy TÖMB(mezo1, mezo2, ..., *), STRING is lehet.
     * A limit paraméterben a A LIMIT alsó és felső értékét lehet megadni, string is lehet
     * Az order paraméterben az sql végi rendezéseket és hasonlókat lehet itt megadni stringben, a LIMIT-et nem
     * A Join paraméter egy tömb, amiben a kapcsolt táblákat lehet megadni illetve az összekapcsolt mezőket.
     * Pl.: TÖMB(tömb('a kijelölt tábla', 'a kijelölt tábla kacsolatmezője', 'kapcsolat tábla neve', 'kapcsolat tábla aliasa', 'kapcsolat tábla kapcsolatmezője','kapcsolat típusa'), ...)
     * @example array(array(TB_CIKK_NYELV, "id",TB_CIKK, "id"))
     * eredmény: JOIN TB cikk_nyelv ON TB_CIKK.id = TB_cikknyelv.id
     */
    public function select($what, $table, $where = "1", $limit = array(), $order = "", $join = array()) {
        $mit = "";
        $kapcsolva = "";
        $korlat = "";
        if (is_array($what)) {
            foreach ($what as $k) {
                $mit .= ($mit != "") ? ", " : "";
                $mit .= $k;
            }
        } else {
            $mit = $what;
        }
        if (is_array($join)) {
            foreach ($join as $k) {
                if (is_array($k)) {
                    $kapcsolva .= " " . @$k[5] . " JOIN " . $k[2] . " " . ($k[3] ?: $k[2]) . " ON " . $k[0] . "." . $k[1] . "=" . ($k[3] ?: $k[2]) . "." . $k[4];
                } else {
                    $kapcsolva .= $k;
                }
            }
        } else {
            $kapcsolva = $join;
        }
        if (is_array($limit)) {
            foreach ($limit as $m) {
                $korlat .= ($korlat != "") ? ", " : "LIMIT ";
                $korlat .= $m;
            }
        } elseif (is_int($limit)) {
            $korlat = "LIMIT " . (string )$limit;
        } elseif (strlen($limit) > 0) {
            $korlat = "LIMIT $limit";
        }
        $query = "SELECT $mit FROM $table $kapcsolva WHERE $where $order $korlat";
        $this->last_query = $query;
        $result = $this->mysqli->query($query);
        if ($result) {
            return $result;
        } else {
            $this->error();
            return false;
        }
    }
    /*---------------------------------------------------------------------------------------------*/

    /**
     * @function protected error
     * @param string|bool|array $eventLog eseménynapló hiba esetén a lekérdezés sora
     * MySQL hibát ír ki, ha DEBUG mód be van kapcsolva
     */
    protected function error() {
        if ($this->debug) {
            echo "MySQL Hiba: " . mysqli_error($this->mysqli) . "<br />";
            echo "MySQL Lekérdezés: " . $this->last_query . "<br />";

        }
    }
    /*---------------------------------------------------------------------------------------------*/

    /**
     * @function public insert
     * @param array $data Adatok tömb
     * @param string $table Tábla név
     * @param bool $errorBack sql errorkódot várja vissza
     * @param array|string $logData Eseménynaplóba illesztendő adatok (optional)
     * @return false|int false or inserted id
     * Egy új rekordod ad hozzá a táblához
     * Az adatok tömbből (asszociatív) ami bemenő paraméter, a tömbindexex lesznek a mezők nevei
     * az értékek pedig amik a mezőkbe kerülnek
     */
    public function insert($data, $table, $errorBack = false) {
        $columns = "";
        $values = "";
        foreach ($data as $column => $value) {
            $columns .= ($columns == "") ? "" : ", ";
            $columns .= "`" . $column . "`";
            $value = htmlentities($value, ENT_QUOTES, "UTF-8");
            if (is_string($value)) {
                $value = "'" . $value . "'";
            }
            $values .= ($values == "") ? "" : ", ";
            $values .= $value;
        }
        $query = "INSERT INTO $table ($columns) VALUES ($values)";
        $this->last_query = $query;
        if ($this->query($query)) {
            return $this->inserted_id();
        }
        if ($errorBack) {
            return array('code' => $this->mysqli->errno, 'message' => $this->mysqli->error);
        }
        return false;
    }
    /*---------------------------------------------------------------------------------------------*/

    /**
     * @function protected query
     * @param string $query lekérdezés
     * @param array|string $logData Eseménynaplóba illesztendő adatok (optional)
     * @return bool
     * MySQLi osztály query függvényét futtatja, és eseménynaplóban rögzíti a lekérdezést
     */
    protected function query($query) {
        if ($this->mysqli->query($query)) {
            return true;
        }
        $this->error();
        return false;
    }

    /**
     * @function public update
     * @param mixed $data ARRAY (index->mezonev, value->ertek)
     * @param string $table tábla neve
     * @param string $where feltétel
     * @param bool $errorBack sql errorkódot várja vissza
     * @return BOOL
     * @throws Exception
     */
    public function update($data, $table, $where, $errorBack = false) {
        $update = "";
        if (is_array($data)) {
            foreach ($data as $column => $value) {
                $value = htmlentities($value, ENT_QUOTES, "UTF-8");
                if (is_string($value)) {
                    $value = "'" . $value . "'";
                }
                $update .= ($update == "") ? "`$column` = $value" : ", `$column` = $value";
            }
        } else {
            throw new InvalidArgumentException("db update: bementi formátum helytelen");
        }
        $query = "UPDATE $table SET $update WHERE $where";
        $this->last_query = $query;
        if (!($ret = $this->query($query))) {
            if ($errorBack) {
                return array('code' => $this->mysqli->errno, 'message' => $this->mysqli->error);
            }
        }
        return $ret;
    }

    /**
     * @function public delete
     * @param string $table tábla neve (valami)
     * @param string $where feltétel (asd=xy)
     * @param int|bool $limit limit (LIMIT 10)
     * @return BOOL
     */
    public function delete($table, $where, $limit = false) {
        $query = "DELETE FROM $table WHERE $where " . ($limit ? "LIMIT $limit" : "");
        $this->last_query = $query;
        return $this->query($query);
    }
    //## PROPERTYk kiolvasása ##


    /**
     * @function public affected_rows
     * @return int Módosított rekordok száma
     * Módosított rekordok száma (Update, Delete..)
     */
    public function affected_rows() {
        return $this->mysqli->affected_rows;
    }

    /**
     * @function public inserted_id
     * @return int insert_id
     * Utoljára beszúrt autó incrementes mező id-jével tér vissza
     */
    public function inserted_id() {
        return $this->mysqli->insert_id;
    }
}