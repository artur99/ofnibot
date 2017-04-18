<?php

// Router list
// $app->mount('/', new Controllers\SectController());

$app->mount('/', new Controllers\IndexController());
$app->mount('/demo', new Controllers\DemoController());
$app->mount('/app', new Controllers\AppController());
$app->mount('/ajax', new Controllers\AjaxController());
