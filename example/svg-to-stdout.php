<?php
require_once(__DIR__ . "/../vendor/autoload.php");

use \HieroTeX\Hieroglyph\Inscription;

header("Content-Type: image/svg+xml");
$inscription = new Inscription("i-mn:n-Htp:t*p");
echo $inscription -> toSvg();
