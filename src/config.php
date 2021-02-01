<?php
//define('PROJECT_ROOT_DIR', __DIR__ . '/..');
define('PROJECT_ROOT_DIR', __DIR__ . DIRECTORY_SEPARATOR . '..');

if (($_SERVER['SERVER_NAME'] == 'bj'))
{
    define('HTML_TEMPLATES_PATH', __DIR__ . DIRECTORY_SEPARATOR . 'html'. DIRECTORY_SEPARATOR );
    define('DB_HOST', 'localhost');
    define('DB_NAME', 'mvc');
    define('DB_USER', 'root');
    define('DB_PASSWORD', '');
}else{

    define('HTML_TEMPLATES_PATH', __DIR__ . DIRECTORY_SEPARATOR . 'www'. DIRECTORY_SEPARATOR );
    define('DB_HOST', 'mysql.zzz.com.ua');
    define('DB_NAME', 'tatik');
    define('DB_USER', 'tatik');
    define('DB_PASSWORD', '1234561q2w3E');
}

