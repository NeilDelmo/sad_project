<?php
require __DIR__.'/../vendor/autoload.php';

$u = new App\Models\User();
$ref = new ReflectionClass($u);
$file = $ref->getFileName();
$contents = @file_get_contents($file) ?: '';
var_export([
    'class' => get_class($u),
    'file' => $file,
    'has_method' => method_exists($u, 'fishermanProfile'),
    'methods_sample' => array_slice(get_class_methods($u), 0, 20),
    'traits' => array_keys($ref->getTraits()),
    'contains_text_fishermanProfile' => strpos($contents, 'function fishermanProfile') !== false,
    'md5' => md5($contents),
    'head200' => substr($contents, 0, 200),
]);
echo "\n";