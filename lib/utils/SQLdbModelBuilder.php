<?php
$withValidation = true;
$regex = array(
    'extract_table' => '/(CREATE TABLE[\S\s]*?);/',
    'extract_table_name' => '/^.*? `(.*?)` \(/',
    'extract_field_information' => '/`(.*?)` (int|varchar|blob)\(?(\d{1,3}?)?\)?(.*)?/',
    'extract_primary_key' => '/ALTER TABLE `(.*?)`\s*ADD PRIMARY KEY \(`(.*?)`\);/',
    'extract_key_ai' => '/ALTER TABLE `(.*?)`\s*?MODIFY `(.*?)` (int|varchar)\((\d{1,3})\)(.*?)AUTO_INCREMENT;/'
);
$sql = file_get_contents('test/sql-test3.sql');
$tablebuild = array();
preg_match_all($regex['extract_table'], $sql, $matches);//Get table names
foreach($matches[1] as $table){
    $tablename = ''; //init
    $formattable = str_replace("\n", "", $table); //Remove newlines for better matchint
    preg_match($regex['extract_table_name'], $formattable, $tblnmmatch); //Extract all tables
    if(array_key_exists(1, $tblnmmatch)){ //validate match

        $tablename = $tblnmmatch[1]; //set tblname
        $tablebuild[$tablename] = array(); //set final output too

        $formattable = substr($formattable,strpos($formattable,'(')+1); //Remove create
        $formattable = substr($formattable,0,strrpos($formattable, ')', -1)); //remove db misc
        $formatexplode = explode(',',$formattable); //Split out columns
        foreach($formatexplode as $field){
            preg_match($regex['extract_field_information'], $field, $fieldmatches); //Extract fields
            $tablebuild[$tablename]['fields'][$fieldmatches[1]] = array( //Select by tablename and set the field to an array
                'type' => $fieldmatches[2], //int|varchar implemented
                'length' => $fieldmatches[3],
                'extra' => $fieldmatches[4]  //Usually contains not null etc
            );

        }
    }else{
        echo 'Could not find array key';
    }
}
preg_match_all($regex['extract_primary_key'], $sql, $primarykeys); //Get primaries
for($x = 0;$x < count($primarykeys[1]); $x++){ //Loop through using the total count of matches
    $tablebuild[$primarykeys[1][$x]]['fields'][$primarykeys[2][$x]]['primarykey'] = true; //Set the field in the table to have the property primarykey=true
    $tablebuild[$primarykeys[1][$x]]['primarykey'] = $primarykeys[2][$x]; //Set the table property=field
}
preg_match_all($regex['extract_key_ai'], $sql, $ai); //Match out autoincrement
for($x = 0;$x < count($ai[1]); $x++){
    $tablebuild[$ai[1][$x]]['fields'][$ai[2][$x]]['auto_increment'] = true; //Same as usual
}
foreach($tablebuild as $tablename => $arr){
    //Build the code
    echo '<pre>';
    echo genSelectSingle($tablename);
    echo genSelectMulti($tablename);
    echo genInsert($tablename, $arr);
    echo genUpdate($tablename);
    echo '</pre>';
}
function genSelectSingle($tablename){
    $funcname = $tablename[strlen($tablename)-1] == 's' ? substr($tablename, 0, strlen($tablename)-1) : $tablename;
    $funcname = removeSnailcase(array(), $funcname);
    $funcname = strtoupper(substr($funcname, 0, 1)).substr($funcname, 1);
$str = '
public function select'.$funcname.'($array){
    $query = \'SELECT * FROM '.$tablename.'\';
    $ret = $this->whereOrAndClause($query, $array, array());
    $query = $ret[\'query\'];
    $preparray = $ret[\'preparray\'];
    return $this->execSelectSingle($query, $preparray);
}';
return $str;
}
function genSelectMulti($tablename){
    $funcname = $tablename[strlen($tablename)-1] == 's' ? $tablename : $tablename.'s';
    $funcname = removeSnailcase(array(), $funcname);
    $funcname = strtoupper(substr($funcname, 0, 1)).substr($funcname, 1);
    $str = '
public function select'.$funcname.'($array, $limit = 0, $offset = 0){
    $query = \'SELECT * FROM '.$tablename.'\';
    $ret = $this->whereOrAndClause($query, $array, array());
    $query = $ret[\'query\'];
    $preparray = $ret[\'preparray\'];
    return $this->execSelectLimit($query, $preparray, $limit, $offset);
}';
  return $str;
}
function genUpdate($tablename){
    //$funcname = $tablename[strlen($tablename)-1] == 's' ? $tablename : $tablename.'s';
    $funcname = removeSnailcase(array(), $tablename);
    $funcname = strtoupper(substr($funcname, 0, 1)).substr($funcname, 1);
    $str = '
public function update'.$funcname.'($array, $update){
    $query = \'UPDATE '.$tablename.' SET\';
    $ret = $this->updateClause($query, $update);
    $query = $ret[\'query\'];
    $updatearr = $ret[\'updatearr\'];
    $ret = $this->whereOrAndClause($query, $array);
    $query = $ret[\'query\'];
    $preparray = $ret[\'preparray\'];
    return $this->execUpdate($query, $array, $updatearr);
}';
return $str;
}
function genInsert($tablename, $arr){
    global $withValidation;
    $fields = array();
    $defaults = array('created_at' => 'CURR_TIME', 'modified_at' => '0');

    foreach($arr['fields'] as $k => $v){
        if(!$v['auto_increment']){
            $fields[$k] = $v;
        }
    }
    /*
    //Possibly add this in to have a codeconsistent and better name,
    //but only works if you label your dbs like i do, but this tool is for me so...
    //at the same time i dont do my code the same way every time.

    */
    $funcname = removeSnailcase(array(), $tablename);
    $str = '
public function insert'.(
        strtolower(
            $funcname[
                strlen($funcname)-1
            ]
        ) == 's'
     ? strtoupper(
        substr(
            $funcname,
            0,
            1
        )
    ).substr(
            $funcname,
            1,
            strlen($funcname)-2
        ) : strtoupper(
        substr(
            $funcname,
            0,
            1
        )
    ).substr($funcname, 1)).'(';
    $count = 0;
    $fields = sortFields($fields, $defaults);
    foreach($fields as $k => $v){
        if($count > 0){
            $str .= ', ';
        }
        $str .= "\$$k";
        if(array_key_exists($k, $defaults)){
            $str .= " = {$defaults[$k]}";
        }
        if($count == 0){
            $count++;
        }
    }
    $str .= '){
    $errors = array();';
    if($withValidation){
        foreach($fields as $k => $v){
            if($v['type'] == 'varchar'){
                $str .="
    if(!is_string(\$$k) || !(strlen(\$$k) <= {$v['length']})){
        \$errors['$k'] = array('Invalid input for $k');
    }";

            }elseif($v['type'] == 'int'){
                $str .="
    if(!(\$this->isSQLInt(\$$k)) && !(strlen(\$$k) <= {$v['length']})){
        \$errors['$k'] = array('Invalid input for $k');
    }";
            }
            if(array_key_exists($k, $defaults)){
                $str .= "
    if(\$$k == 'CURR_TIME'){
        \$$k = time();
    }";
            }
        }
    }
    $sa = array();
    foreach($fields as $k => $v){
        $sa[] = $k;
    }
    $str .= "
    if(count(\$errors) > 0){
        return array('type' => 'error', 'return' => \$errors);
    }
    \$query = 'INSERT INTO $tablename (".implode(", ", $sa).')
    VALUES (';
    $sa[0] = ':'.$sa[0];
    $str .= implode(', :', $sa).')\';';
    $sa[0] = substr($sa[0], 1);
    $str .= '
    $exarr = array(';
    $count = 1;
    foreach($sa as $e){
        $str .= "
        ':$e' => \$$e";
        if(count($sa) != $count){
            $str .= ',';
        }
        $count++;
    }
    $str .= '
    );
    return $this->execInsert($query, $exarr);
}';
    return $str;
}
function isSQLInt($var){
    $inttostr = "".$var;
    if($inttostr[0] === '-'){
        $cut = substr($inttostr, 1);
        return ctype_digit($cut);
    }
    return ctype_digit($var);
}
function sortFields($fields, $defaults){
    $arr = array();
    $before = array();
    $lastargs = array();
    foreach($fields as $k => $v){
        if(array_key_exists($k, $defaults)){
            $lastargs[$k] = $v;
        }else{
            if(strpos($k, '_') > -1){
                $before[$k] = $v;
            }else{
                $arr[$k] = $v;
            }
        }
    }
    return array_merge(array_merge($arr, $before), $lastargs);
}
function removeSnailcase($prefixes, $string){
    $funcname = '';
    $funcnamepos = strpos($string, '_');
    if($funcnamepos > -1){
        if(strlen(substr($string, $funcnamepos)) > 2){
            $ex = explode('_', $string);
            $cap = '';
            foreach(array_slice($ex, 1) as $elem){
                $cap .= capStr($elem);
            }
            $funcname = $ex[0].$cap;
        }
    }
    if($funcname == ''){
        $funcname = $string;
    }
    return $funcname;
}
function capStr($string){
    return strtoupper(substr($string, 0, 1)).strtolower(substr($string, 1));
}
?>
