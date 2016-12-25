<?php
$mysql = array(
  'user' => 'root',
  'password' => 'mysql',
  'host' => '127.0.0.1',
  'db' => 'ssm'
);
$general = array(
  'baseurl' => '/ssm/'
);
$routes = array(
    '/ping' => array(
        'function' => 'ping',
        'description' => 'returns ping'
    ),
    '/getMessages' => array(
        'function' => 'getMessages',
        'description' => 'returns messages'
    ),
    '/sendMessage' => array(
        'function' => 'sendMessage',
        'description' => 'Sends a message'
    ),
    '/login' => array(
        'function' => 'login'
        'description' => 'Creates a session and logs in the user'
    ),
    '/docs' => array(
        'function' => 'getRoutes'
    )
);
?>
