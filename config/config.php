<?php // config.php

//define('BASE_PATH', dirname(__DIR__));
define('BASE_URL', '/users'); // adjust if needed

$localConfig = __DIR__ . '/config.local.php';
if (file_exists($localConfig)) {
    require $localConfig;
    return;
}

$prodConfig = __DIR__ . '/config.prod.php';
if (file_exists($prodConfig)) {
    require $prodConfig;
    return;
}

throw new RuntimeException('No config found: create config.local.php (local) or config.prod.php (production).');