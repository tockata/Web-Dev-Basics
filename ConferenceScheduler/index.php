<?php
ini_set('display_errors', 1);

use Framework\App;

require_once 'Framework/App.php';

$app = App::getInstance();
$app->run();