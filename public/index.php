<?php
ini_set("default_charset", "UTF-8");

include '..' . DIRECTORY_SEPARATOR . 'application' . DIRECTORY_SEPARATOR . 'Bootstrap.php';

$objBoot = new Bootstrap();
$objBoot->runApp();