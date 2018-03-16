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

new Server();
?>