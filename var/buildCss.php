<?php

require_once 'src/main/php/includes/libraries/autoload.php';

use MatthiasMullie\Minify;

$base = realpath(dirname(__FILE__) . '/../src/main/php/resources/stylesheets/') . '/';

$minifier = new Minify\CSS();
$minifier->add($base . 'main.css');
$minifier->add($base . 'hud.css');
echo $minifier->minify();
echo "done";

?>
