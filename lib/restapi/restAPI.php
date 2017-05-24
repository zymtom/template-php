<?php
class restAPI {
    private $basepath = '';
    private $routes = array();
    public function __construct($basepath, $routes){
        if(strpos($_SERVER['REQUEST_URI'], $basepath) !== 0){
            http_response_code(500);
            die('bad basepath');
        }
        if(!is_array($routes)){
            http_response_code(500);
            die('bad route');
        }
        if(!(count($routes) > 0)){
            http_response_code(500);
            die('no route');
        }

        global $site;
        $this->basepath = $basepath;
        $ret = $this->registerRoutes($routes);
        if(!$ret[0]){
            http_response_code(500);
            die(json_encode($ret[1]));
        }
    }
    public function getPath(){
        global $site;
        if(!array_key_exists(substr($_SERVER['REQUEST_URI'], strlen($this->basepath)-1), $this->routes)){
            http_response_code(404);
            return array(false, array('No matching route'));
        }

        $res = $site->{$this->routes[substr($_SERVER['REQUEST_URI'], strlen($this->basepath)-1)]['function']}();
        if(!$res[0]){
            http_response_code(500);
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
