<?php

$settings = require __DIR__ . '/defaults.php';

//Check for a development environment config file, or fallback to the production
//file, if it exists.
if (isset($_ENV['APP_ENV'])) {
    if (file_exists(__DIR__ . '/conf/' . strtolower($_ENV['APP_ENV']) . '.php')) {
        require_once __DIR__ . '/conf/' . strtolower($_ENV['APP_ENV']) . '.php';
    }
} else {
    if (file_exists(__DIR__ . '/conf/prod.php')) {
        require_once __DIR__ . '/conf/prod.php';
    }
}

//TODO: Move this to the container?
require_once __dir__  . "/version.php";
$settings['app']['version'] = VERSION_MAJOR . '.' . VERSION_MINOR . '.' . VERSION_PATCH . VERSION_TAG;

if (file_exists(__DIR__ . '/conf/servers.json')) {
    $settings['servers'] = json_decode(file_get_contents(__DIR__ . '/conf/servers.json'));
}
if (file_exists(__DIR__ . '/conf/ranks.json')) {
    $settings['ranks'] = json_decode(file_get_contents(__DIR__ . '/conf/ranks.json'));
}


return $settings;
