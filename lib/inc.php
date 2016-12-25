<?php
include('config.php');
include('ssm.db.class.php');
$db = new ssmDB($mysql);
include('site.class.php');
$site = new ssm($db);
$routes = array(
    '/ping' => array(
        'function' => 'ping',
        'description' => 'returns ping'
    ),
    '/getMessages' => array(
        'function' => 'getMessages'
    ),
    '/sendMessage' => array(
        'function' => 'sendMessage'
    ),
    '/login' => array(
        'function' => 'login'
    )
);
//include('views/init.php');
