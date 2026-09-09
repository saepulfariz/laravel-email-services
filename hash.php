<?php

$apikey = '12345678';
$hashedKey = hash('sha256', $apikey);

echo $hashedKey;
