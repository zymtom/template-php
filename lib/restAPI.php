<?php
class restAPI {
    private $basepath = '';
    private $routes = array();
    public function __construct($basepath, $routes){
        if(strpos($_SERVER['REQUEST_URI'], $basepath) !== 0){
            header("HTTP/1.1 500 Internal Server Error");
            die('bad basepath');
        }
        if(!is_array($routes)){
            header("HTTP/1.1 500 Internal Server Error");
            die('bad route');
        }
        if(!(count($routes) > 0)){
            header("HTTP/1.1 500 Internal Server Error");
            die('no route');
        }

        global $site;
        $this->basepath = $basepath;
        $ret = $this->registerRoutes($routes);
        if(!$ret[0]){
            header("HTTP/1.1 500 Internal Server Error");
            die(json_encode($ret[1]));
        }
    }
    public function getPath(){
        global $site;
        if(!array_key_exists(substr($_SERVER['REQUEST_URI'], strlen($this->basepath)-1), $this->routes)){
            header("HTTP/1.1 500 Internal Server Error");
            return array(false, array('No matching route'));
        }

        $res = $site->{$this->routes[substr($_SERVER['REQUEST_URI'], strlen($this->basepath)-1)]['function']}();
        if(!$res[0]){
            header("HTTP/1.1 500 Internal Server Error");
        }
        return $res;
    }
    private function registerRoutes($routes){
        foreach($routes as $key => $val){
            global $site;
            if(!method_exists($site, $val['function'])){
                return array(false, array("method $val[function] doesn't exist"));
            }
        }
        $this->routes = $routes;
        return array(true, "");
    }
}
