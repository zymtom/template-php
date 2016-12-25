<?php
include("lib/db.class.php");
class ssmDB extends DB{
    public function __construct($db, $debug = false){
        parent::__construct($db, $debug);
    }
    public function selectUser($array){
        $query = 'SELECT * FROM users';
        $ret = $this->whereOrAndClause($query, $array, array());
        $query = $ret['query'];
        $preparray = $ret['preparray'];
        return $this->execSelectSingle($query, $preparray);
    }
    public function selectUsers($array, $limit = 0, $offset = 0){
        $query = 'SELECT * FROM users';
        $ret = $this->whereOrAndClause($query, $array, array());
        $query = $ret['query'];
        $preparray = $ret['preparray'];
        return $this->execSelectLimit($query, $preparray, $limit, $offset);
    }
    public function insertUser($username, $publickey, $email, $hash){
        $errors = array();
        if(!is_string($username) || !(strlen($username) <= 60)){
            $errors['username'] = array('Invalid input for username');
        }
        if(!is_string($email) || !(strlen($email) <= 60)){
            $errors['email'] = array('Invalid input for email');
        }
        if(!is_string($hash) || !(strlen($hash) <= 72)){
            $errors['hash'] = array('Invalid input for hash');
        }
        if(count($errors) > 0){
            return array('type' => 'error', 'return' => $errors);
        }
        $query = 'INSERT INTO users (username, , email, hash)
        VALUES (:username, :publickey, :email, :hash)';
        $exarr = array(
            ':username' => $username,
            ':publickey' => $publickey,
            ':email' => $email,
            ':hash' => $hash
        );
        return $this->execInsert($query, $exarr);
    }
    public function updateUsers($array, $update){
        $query = 'UPDATE users SET';
        $ret = $this->updateClause($query, $update);
        $query = $ret['query'];
        $updatearr = $ret['updatearr'];
        $ret = $this->whereOrAndClause($query, $array);
        $query = $ret['query'];
        $preparray = $ret['preparray'];
        return $this->execUpdate($query, $array, $updatearr);
    }
    public function selectMessage($array){
        $query = 'SELECT * FROM messages';
        $ret = $this->whereOrAndClause($query, $array, array());
        $query = $ret['query'];
        $preparray = $ret['preparray'];
        return $this->execSelectSingle($query, $preparray);
    }
    public function selectMessages($array, $limit = 0, $offset = 0){
        $query = 'SELECT * FROM messages';
        $ret = $this->whereOrAndClause($query, $array, array());
        $query = $ret['query'];
        $preparray = $ret['preparray'];
        return $this->execSelectLimit($query, $preparray, $limit, $offset);
    }
    public function insertMessage($message, $sender_id, $reciever_id, $sent_at, $expire_after, $expire_time){
        $errors = array();
        if(!($this->isSQLInt($sender_id)) && !(strlen($sender_id) <= 1)){
            $errors['sender_id'] = array('Invalid input for sender_id');
        }
        if(!($this->isSQLInt($reciever_id)) && !(strlen($reciever_id) <= 1)){
            $errors['reciever_id'] = array('Invalid input for reciever_id');
        }
        if(!($this->isSQLInt($sent_at)) && !(strlen($sent_at) <= 1)){
            $errors['sent_at'] = array('Invalid input for sent_at');
        }
        if(!($this->isSQLInt($expire_after)) && !(strlen($expire_after) <= 1)){
            $errors['expire_after'] = array('Invalid input for expire_after');
        }
        if(!($this->isSQLInt($expire_time)) && !(strlen($expire_time) <= 1)){
            $errors['expire_time'] = array('Invalid input for expire_time');
        }
        if(count($errors) > 0){
            return array('type' => 'error', 'return' => $errors);
        }
        $query = 'INSERT INTO messages (message, sender_id, reciever_id, sent_at, expire_after, expire_time)
        VALUES (:message, :sender_id, :reciever_id, :sent_at, :expire_after, :expire_time)';
        $exarr = array(
            ':message' => $message,
            ':sender_id' => $sender_id,
            ':reciever_id' => $reciever_id,
            ':sent_at' => $sent_at,
            ':expire_after' => $expire_after,
            ':expire_time' => $expire_time
        );
        return $this->execInsert($query, $exarr);
    }
    public function updateMessages($array, $update){
        $query = 'UPDATE messages SET';
        $ret = $this->updateClause($query, $update);
        $query = $ret['query'];
        $updatearr = $ret['updatearr'];
        $ret = $this->whereOrAndClause($query, $array);
        $query = $ret['query'];
        $preparray = $ret['preparray'];
        return $this->execUpdate($query, $array, $updatearr);
    }
}
