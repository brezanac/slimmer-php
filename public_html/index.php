<?php
/**
 * Main entry point of the application.
 *
 * @author Miša Brežanac <brezanac@gmail.com>
 */

use App\Core\Bootstrap;

require "../vendor/autoload.php";

$bootstrap = new Bootstrap();
$bootstrap->run();
