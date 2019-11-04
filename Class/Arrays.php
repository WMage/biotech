<?php

/**
 * @author White Mage
 * @copyright 2011
 */
class Arrays extends Singleton {
    /*---------------------------------------------------------------------------------------------*/
    /**
     * Tömb elemeinek kiírása
     * @param result
     */
    public function dump_array($tomb) {
        foreach ($tomb as $t) {
            print_r($t);
            echo "<br>-------------------<br>";
        }
    }
    /*---------------------------------------------------------------------------------------------*/
    /**
     * Function query_result_to_array
     * @param mysqli_result $result
     * @param bool $single_row
     * @return array ARRAY //Assoc
     * Feldolgozza a MySQL Row Setet (resource tip) és visszatér egy asszocitív tömmbel, ahol az
     * tömb index (key) = mező neve és az érték (value) = rekord
     * Ha $singleRow értéke igaz, akkor csak 1 rekorddal tér vissza a tömb helyett
     */
    public function query_result_to_array($result, $single_row = false) {
        $return = array();

        while ($row = $result->fetch_assoc()) {
            array_push($return, $row);
        }

        if ($single_row === true) {
            if (count($return) > 0) {
                return $return[0];
            }
            return array();
        }

        return $return;
    }
    /*---------------------------------------------------------------------------------------------*/
    /**
     * Function filter_by_value
     * @param array $array Szűrendő tömb (ARRAY)
     * @param array $index_value Keresett érték (Index és érték) (ARRAY("index1"=>'ertek1',"index2"=>'ertek2'))
     * @param bool $bool ha csak értesítés-t kérek róla hogy a keresett érték megtalálhatóe a tömben (BOOL)
     * @param int $operator (1-azonos értékek, 2-nemazonos, 3-kisebb, 4-nagyobb, 5-tartalmazza) (INT)
     * @param bool $first Az 1. találattal térjen vissza? (BOOL)
     * @param bool $or Keresési kapcsoalt típusa: true=OR, false=AND
     * @return array Eredmény tömb (ARRAY) vagy BOOL
     * Tömbön belülei(tomb1) associatív tömbben(tomb2) történő keresése,
     * egyezés esetén az egész tomb2-t visszaadja az adott tomb1 indexen
     */
    public function filter_by_value($array, $index_value, $bool = false, $operator =
    1, $first = false, $or = false) {
        $indexek = array_keys($index_value);
        $feltetel_db = count($indexek);
        $newarray = array();
        if (is_array($array) && count($array) > 0) {
            switch ($operator) {
                case 1:
                    foreach (array_keys($array) as $key) {
                        $teljesul = 0;
                        foreach ($indexek as $index) {
                            if ($array[$key][$index] == $index_value[$index]) {
                                $teljesul++;
                            }
                        }
                        if (($or) ? ($teljesul > 0) : ($teljesul == $feltetel_db)) {
                            if ($bool) {
                                return true;
                            }
                            $newarray[$key] = $array[$key];
                            if ($first) {
                                return $newarray[$key];
                            }
                        }
                    }
                    break;
                case 2:
                    foreach (array_keys($array) as $key) {
                        $teljesul = 0;
                        foreach ($indexek as $index) {
                            if ($array[$key][$index] != $index_value[$index]) {
                                $teljesul++;
                            }
                        }
                        if (($or) ? ($teljesul > 0) : ($teljesul == $feltetel_db)) {
                            if ($bool) {
                                return true;
                            }
                            $newarray[$key] = $array[$key];
                            if ($first) {
                                return $newarray[$key];
                            }
                        }
                    }
                    break;
                case 3:
                    foreach (array_keys($array) as $key) {
                        $teljesul = 0;
                        foreach ($indexek as $index) {
                            if ($array[$key][$index] < $index_value[$index]) {
                                $teljesul++;
                            }
                        }
                        if (($or) ? ($teljesul > 0) : ($teljesul == $feltetel_db)) {
                            if ($bool) {
                                return true;
                            }
                            $newarray[$key] = $array[$key];
                            if ($first) {
                                return $newarray[$key];
                            }
                        }
                    }
                    break;
                case 4:
                    foreach (array_keys($array) as $key) {
                        $teljesul = 0;
                        foreach ($indexek as $index) {
                            if ($array[$key][$index] > $index_value[$index]) {
                                $teljesul++;
                            }
                        }
                        if (($or) ? ($teljesul > 0) : ($teljesul == $feltetel_db)) {
                            if ($bool) {
                                return true;
                            }
                            $newarray[$key] = $array[$key];
                            if ($first) {
                                return $newarray[$key];
                            }
                        }
                    }
                    break;
                case 5:
                    foreach (array_keys($array) as $key) {
                        $teljesul = 0;
                        foreach ($indexek as $index) {
                            if (in_array($array[$key][$index], $index_value[$index])) {
                                $teljesul++;
                            }
                        }
                        if (($or) ? ($teljesul > 0) : ($teljesul == $feltetel_db)) {
                            if ($bool) {
                                return true;
                            }
                            $newarray[$key] = $array[$key];
                            if ($first) {
                                return $newarray[$key];
                            }
                        }
                    }
                    break;
            }
        }
        if ($bool) {
            return false;
        }
        return $newarray;
    }
    /*---------------------------------------------------------------------------------------------*/
    /**
     * Function clear_duplicates
     * @param string $index index (String)
     * @param array $arr tisztítandó tömb (reference ARRAY)
     * 2 dimenziós tömb 2. dimenzióján a megadott indexen szűri az imétlődéseket és
     * mindegyikből csak az 1. találatot adja vissza (ha csak 1 van, akkor is)
     */
    public function clear_dublicates($index, &$arr) {
        $used = array();
        $ret = array();
        foreach ($arr as $a) {
            if (!in_array($a[$index], $used)) {
                array_push($ret, $a);
                array_push($used, $a[$index]);
            }
        }
        $arr = $ret;
    }
    /*---------------------------------------------------------------------------------------------*/
    /**
     * Function array_diff_assoc_recursive
     * @param array $array1 Összehasonlítandó tömb 1 (ARRAY)
     * @param array $array2 Összehasonlítandó tömb 2 (ARRAY)
     * @return array Eredmény tömb (ARRAY)
     * array_diff_assoc php függvény többdienziós asszociatív ömbön is működő változata
     */
    public function array_diff_assoc_recursive($array1, $array2) {
        $difference = array();
        foreach ($array1 as $key => $value) {
            if (is_array($value)) {
                if (!isset($array2[$key]) || !is_array($array2[$key])) {
                    $difference[$key] = $value;
                } else {
                    $new_diff = $this->array_diff_assoc_recursive($value, $array2[$key]);
                    if (!empty($new_diff))
                        $difference[$key] = $new_diff;
                }
            } else
                if (!array_key_exists($key, $array2) || $array2[$key] !== $value) {
                    $difference[$key] = $value;
                }
        }
        return $difference;
    }

}

?>