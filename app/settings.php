<?php

$settings = require __DIR__ . '/defaults.php';

if(file_exists(__DIR__ . '/conf/local.php')) {
    require_once(__DIR__ . '/conf/local.php');
} elseif (file_exists(__DIR__ . '/conf/dev.php')) {
    require_once(__DIR__ . '/conf/dev.php');
} elseif (file_exists(__DIR__ . '/conf/test.php')) {
    require_once(__DIR__ . '/conf/test.php');
} elseif (file_exists(__DIR__ . '/conf/prod.php')) {
    require_once(__DIR__ . '/conf/prod.php');
}

//TODO: Move this to the container?
require_once __dir__  . "/version.php";

$settings['app']['version'] = VERSION_MAJOR . '.' . VERSION_MINOR . '.' . VERSION_PATCH . VERSION_TAG;

return $settings;
