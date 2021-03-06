<?php

/**
 * Carga de archivos de configuración.
 * Comprueba si existe el archivo config.php, si no existe redirecciona
 * a la pagina de instalación.
 */
/** Ruta absoluta del proyecto. */
define('ABSPATH', dirname(__FILE__) . DIRECTORY_SEPARATOR);
$config = ABSPATH . 'config.php';

if (is_readable($config)) {
    require $config;
}else{
    define('INSTALL', 1);
}

require ABSPATH . 'load.php';
