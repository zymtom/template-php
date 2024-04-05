<?php
class ssm {
  public $db = "";
  public $user = "";
  public $groups = "";

  public function __construct($htpdb){
      $this->db = $htpdb;
      session_start();
  }
  public function session($var){
    return $_SESSION[$var];
  }
  public function register($username, $email, $password, $passwordv){
    $errors = array();
    if(empty($username) || !(strlen($username) >= 3)){
      $errors[] = "Username too short.";
    }
    if(empty($password) || !(strlen($password) >= $general['passwordlength'])){
      $errors[] = "Password too short.";
    }
    if($password !== $passwordv){
      $errors[] = "Passwords does not match.";
    }
    if(count($errors) > 0){
      return array(false, $errors);
    }
    if($stmt = $this->db->selectUser(array(array('username' => $username), array('email' => $email)))){
      //Check user
      if($stmt['type'] == 'noreturn'){
        $options = [
          'cost' => 12,
        ];
        if($stmts = $this->db->insertUser($username, $email, password_hash($password, PASSWORD_BCRYPT, $options), 1, $_SERVER['REMOTE_ADDR'])){
          if($stmts['type'] == 'insertid'){
            return array(true, "Created user successfully");
          }elseif($stmts['type'] == 'error'){
            return array(false, array("Failed to create user"));
          }else{
            return array(false, array("Unknown error"));
          }
        }else{
          return array(false, array("Unknown error"));
        }
      }elseif($stmt['type'] == 'sqlresp'){
        return array(false, array("User already exists"));
      }elseif($stmt['type'] == 'error'){
        return array(false, $stmt['return']);
      }
    }else{
      return array(false, array("Unknown error"));
    }
  }
  public function login($username, $password){
    $errors = array();
    $error = false;
    if(empty($username) || !(strlen($username) >= 3)){
      $errors[] = "Username too short.";
      $error = true;
    }
    if(empty($password) || !(strlen($password) >= $general['passwordlength'])){
      $errors[] = "Password too short.";
      $error = true;
    }
    if($error){
      return array(false, $errors);
    }
    if($stmt = $this->db->selectUser(array('username' => $username))){
      if($stmt['type'] == 'sqlresp'){
        if(password_verify($password, $stmt['return']['password'])){
          //Sign in
          $_SESSION['loggedin'] = 1;
          $_SESSION['user'] = $stmt['return'];
          $this->user = $stmt['return'];
          $_SESSION['updated'] = time();
          return array(true, "Successfully signed in.");
        }else{
          //Password incorrect
          return array(false, array('No user with these credentials'));
        }
      }elseif($stmt['type'] == 'noreturn'){
        //No user with username
        return array(false, array('No user with these credentials'));
      }else{
        return array(false, array('Unknown error occurred'));
      }
    }
  }
  public function refreshSession(){
    if($_SESSION['loggedin'] && !empty($_SESSION['updated']) && $_SESSION['updated'] < time()-300){
      if($stmt = $this->db->selectUser(array('id' => $_SESSION['user']['id']))){
        if($stmt['type'] == 'sqlresp'){
          $_SESSION['user'] = $stmt['return'];
          $this->user = $stmt['return'];
          $_SESSION['updated'] = time();
          return array(true, 'Refreshed session');
        }elseif($stmt['type'] == 'noreturn'){
          //No user with username
          return array(false, array('No user with these credentials'));
        }else{
          return array(false, array('Unknown error occurred'));
        }
      }else{
        return array(false, array('unknown error occurred'));
      }
    }
    return array(true, 'Nothing to refresh yet.');
  }
  public function getMessages(){
      if($_SESSION['loggedin']){
          if($stmt = $this->db->selectMessages(array('reciever_id' => $_SESSION['user']['id']))){
              if($stmt['type'] == 'sqlresp'){
                  return array(true, $stmt['return']);
              }elseif($stmt['type'] == 'noreturn'){
                  return array(true, array());
              }elseif($stmt['type'] == 'error'){
                  return array(false, $stmt['return']);
              }else{
                  return array(false, array('Unknown error when selecting messages'));
              }
          }else{
              return array(false, array('Unknown error when calling select message'));
          }
      }else{
          return array(false, array('not logged in'));
      }
  }
  public function isUserAdmin($userid){
      if($stmt = $this->db->selectUserGroup(array(array('user_id' => $userid, 'group_id' => 99)))){

      }
  }
  public function fetchGroupsForUser($userid){
      if($stmt = $this->db->selectUserGroups(array(array('user_id' => $userid)))){
         if($stmt['type'] == 'sqlresp'){
             return array(true, $stmt['return']);
         }elseif($stmt['type'] == 'noreturn'){
             return array(true, array());
         }elseif($stmt['type'] == 'error'){
             return array(false, array('failed to query'));
         }
      }
  }
  public function transferIfNotLoggedIn($page){
    if($_SESSION['loggedin'] !== 1){
      header("Location: $page");
      die();
    }
  }
  public function transferIfLoggedIn($page){
    if($_SESSION['loggedin'] === 1){
      header("Location: $page");
      die();
    }
  }
  public function transferIfNotAdmin($page){
    /*if(!($_SESSION['user']['status'] > 5)){
      header("Location: $page");
      die();
  }*/
  }

  public function ping(){
      //echo 'meme';
      return array(true, 'pong');
  }
}
