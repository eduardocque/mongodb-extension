<?php

use mongodb\tests\helpers\MongoJsonParser;

$contents = file_get_contents(dirname(__FILE__) . '/../json/custom_collection.json');
$json = json_decode($contents, JSON_UNESCAPED_SLASHES|JSON_FORCE_OBJECT|JSON_NUMERIC_CHECK);

return (new MongoJsonParser($json))->parse();
