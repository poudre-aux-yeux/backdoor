<?php
function register($class) {
  $directories = array("classes");
  foreach ($directories as $directory) {
    $file = $directories . '/' . $class . '.class.php';

    if (is_file($filename)) {
      include $filename;
    }
  }
}

spl_autoload_register("register");
include_once('classes/Daemon.class.php');
include_once('classes/Server.class.php');

new Server();
?>
