<?php
public function updateLevelStarts($array, $update){
    $query = 'UPDATE quiz_level_starts SET';
    $ret = $this->updateClause($query, $update);
    $query = $ret['query'];
    $updatearr = $ret['updatearr'];
    $ret = $this->whereOrAndClause($query, $array);
    $query = $ret['query'];
    $preparray = $ret['preparray'];
    return $this->execUpdate($query, $array, $updatearr);
}
?>
