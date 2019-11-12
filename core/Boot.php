<?php

// ////////// CONSTANTES
define('DS', DIRECTORY_SEPARATOR);

define('PATH_ROOT', __DIR__.DS);

define('APP_PATH',   PATH_ROOT.'..'.DS.'app'.DS);
define('CTRLL_PATH', APP_PATH.'Controller'.DS);
define('VIEW_PATH',  APP_PATH.'View'.DS);
define('CORE_PATH',  PATH_ROOT.'..'.DS.'core'.DS);

define('STORAGE_PATH',  PATH_ROOT.'..'.DS.'storage'.DS);

define('PUBLIC_PATH', PATH_ROOT.'..'.DS.'public'.DS);
define('ASSETS_PATH', PUBLIC_PATH.'assets'.DS);


// ////////// BOOT REQUIRED
// carrega o arquivo com as rotas definidas
include_once(APP_PATH.'routes.php');

// inicializa o sistema com uma instancia da classe Router()
$route = new \Core\Router($routes);