<?php
include __DIR__ . '/../vendor/autoload.php';
include __DIR__ . '/../app/config.php';
include __DIR__ . '/../lib/utilFuncs.php';
date_default_timezone_set(DEFAULT_TIMEZONE);
session_start();

$errorReporting = (DEBUG_MODE) ? E_ALL : 0;
error_reporting($errorReporting);

use App\Core\App;

$app = new App();