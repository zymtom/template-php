<?php
class DB{
    public $db = "";
    public $debug = false;
    public function __construct($mysqlconfig, $debug = false){
        $this->db = $this->genMysql($mysqlconfig);
        $this->debug = $debug;
    }
    public function whereOrAndClause($query, $array, $preparray){
      if(count($array) > 0){
          foreach($array as $key => $val){
            if(is_array($val)){
              if(empty($val)){
                return array('query' => $query, 'preparray' => $preparray);
              }
            }
          }
          $query .= ' WHERE';
          $count1 = 0;
          foreach($array as $key => $value){
              if(is_array($value)){
                if($count1 > 0){
                  $query .= ' OR';
                }
                $count2 = 0;
                $query .= " (";
                foreach($value as $k => $v){
                  if($count2 > 0){
                    $query .= " AND `$k` = :$k";
                    $preparray[":$k"] = $v;
                  }else{
                    $query .= " `$k` = :$k";
                    $preparray[":$k"] = $v;
                  }
                  $count2++;
                }
                $query .= ")";
              }else{
                if(count($preparray) > 0){
                    $query .= " AND `$key` = :$key";
                    $preparray[":$key"] = $value;
                }else{
                    $query .= " `$key` = :$key";
                    $preparray[":$key"] = $value;
                }
              }
            $count1++;
          }
      }
      return array('query' => $query, 'preparray' => $preparray);
    }
    public function notInClause($query, $array, $notarray, $pr = array()){
      if(count($notarray) > 0){
        if(count($array) == 0){
          $query .= ' WHERE';
        }else{
          $query .= ' AND';
        }
        foreach($notarray as $k => $v){
          $query .= " $k NOT IN (";
          foreach($v as $vals){
            if($query[strlen($query)-1] != '('){
              $query .= ',';
            }
            $count = 0;
            while(true){
              if(!array_key_exists(':'.$k.$count, $pr)){
                $query .= ':'.$k.$count;
                $pr[":{$k}{$count}"] = $vals;
                break;
              }
              $count++;
            }
          }
          $query .= ')';
          break;
        }
      }
      return array('query' => $query, 'pr' => $pr);
    }
    public function updateClause($query, $update, $updatearr = array()){
      if(count($update) > 0){
        $count = 0;
        foreach($update as $k => $v){
          if($count > 0){
            $query .= ',';
          }
          $query .= " `$k` = :$k";
          $updatearr[":$k"] = $v;
          $count++;
        }
      }
      return array('query' => $query, 'updatearr' => $updatearr);
    }
    public function execInsert($query, $exarr){
      try {
          $stmt = $this->db->prepare($query);
          $stmt->execute($exarr);
          $lastid = $this->db->lastInsertId();
          $errinfo = $stmt->errorInfo();
          if($errinfo[0] > 0){
              return array('type' => 'error', 'return' => $errinfo[0]);
          }elseif($lastid > 0){
              return array('type' => 'insertid', 'return' => $lastid);
          }else{
              return array('type' => 'error', 'return' => 'Something went wrong.');
          }
       } catch(PDOException $ex){
          return array('type' => 'error', 'return' => $ex->getMessage());
       }
    }
    public function execUpdate($query, $preparray, $updatearr){
      try {
          $stmt = $this->db->prepare($query);
          $stmt->execute(array_merge($preparray, $updatearr));
          $rows = $stmt->rowCount();
          $errinfo = $stmt->errorInfo();
          if($errinfo[0] > 0){
              return array('type' => 'error', 'return' => $errinfo[0]);
          }elseif($rows > 0){
              return array('type' => 'rowsaffected', 'return' => $rows);
          }else{

              return array('type' => 'noreturn', 'return' => 'none');
          }
      } catch(PDOException $ex) {
          return array('type' => 'error', 'return' => $ex);
      }
    }
    public function execSelectLimit($query, $preparray, $limit = 0, $offset = 0){
      //echo $query;
      try {
          $stmt = $this->db->prepare($query);
          foreach($preparray as $k => $v){
            if(ctype_digit($v) || is_int($v)){
              $stmt->bindParam($k, $v, PDO::PARAM_INT);
            }else{
              $stmt->bindParam($k, $v, PDO::PARAM_STR);
            }
          }
          if($limit > 0 || $offset > 0){
            $stmt->bindParam(':lim', $limit, PDO::PARAM_INT);
            $stmt->bindParam(':off', $offset, PDO::PARAM_INT);
          }
          $stmt->execute();
          $rows = $stmt->rowCount();
          $errinfo = $stmt->errorInfo();
          if($errinfo[0] > 0){
              return array('type' => 'error', 'return' => $errinfo[0]);
          }elseif($rows > 0){
              return array('type' => 'sqlresp', 'return' => $stmt->fetchAll(PDO::FETCH_ASSOC));
          }else{

              return array('type' => 'noreturn', 'return' => 'none');
          }
      } catch(PDOException $ex) {
          return array('type' => 'error', 'return' => $ex);
      }
    }
    public function execSelectSingle($query, $preparray){
    //var_dump($query);
    //var_dump($preparray);
    //var_dump($this->genFakeQuery($query, $preparray));
      try {
          $stmt = $this->db->prepare($query);
          if($stmt === false){
              return array('type' => 'error', 'return' => array('failed to prepare query'));
          }
          $stmt->execute($preparray);
          $rows = $stmt->rowCount();
          $errinfo = $stmt->errorInfo();
          if($errinfo[0] > 0){
              return array('type' => 'error', 'return' => array($errinfo[0]));
          }elseif($rows > 0){
              return array('type' => 'sqlresp', 'return' => $stmt->fetch(PDO::FETCH_ASSOC));
          }else{
              return array('type' => 'noreturn', 'return' => 'none');
          }
          //echo $query;
      } catch(PDOException $ex) {
          return array('type' => 'error', 'return' => $ex);
      }
    }
    public function genFakeQuery($query, $array, $limit = 0, $offset = 0){
      foreach($array as $key => $val){
        $query = str_replace($key, "'$val'", $query);
      }
      if($limit > 0 || $offset > 0){
        $query = str_replace(':lim', $limit, $query);
        $query = str_replace(':off', $offset, $query);
      }
      return $query;
    }
    public function genMysql($mysql){
        $options  = array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'UTF8'");
        //var_dump($mysql);
        try {
          $pdo = new PDO("mysql:host={$mysql['host']};dbname={$mysql['db']};charset=utf8", $mysql['user'], $mysql['password'], $options);
          $pdo->setAttribute( PDO::ATTR_EMULATE_PREPARES, false );
          return $pdo;
        }
        catch(Exception $e) {
          //throw $e; // For debug purpose, shows all connection details
          throw new PDOException('Could not connect to database, hiding connection details.'); // Hide connection details.
          //die('Could not connect to database');
        }
    }
    public function isSQLInt($var){
        $inttostr = "".$var;
        if($inttostr[0] === '-'){
            $cut = substr($inttostr, 1);
            return ctype_digit($cut);
        }
        return ctype_digit($var);
    }
}
