<?php

header("Content-Type: application/json");

$basePath = '/kidelicia/public';
$uri = str_replace($basePath, '', $_SERVER['REQUEST_URI']);
$uri = strtok($uri, '?'); // Remove query strings

$method = $_SERVER['REQUEST_METHOD'];

require_once __DIR__ . '/../routes/api.php';
/*
var_dump($uri, $method);
die();
*/