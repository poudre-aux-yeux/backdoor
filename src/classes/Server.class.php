<?php
include_once "Daemon.class.php";
include_once "Config.php";

stream_set_blocking(STDIN, false);

class Server extends Daemon
{

    public $socket = null;
    public $clients = array();
    public $str = "";
    public $size_to_read = 0;
    public $read = 0;

    public function __construct()
    {
        // Ici on souhaite gérer les signaux SIGUSR1 et SIGUSR2 en plus
        parent::__construct("server", array(
            SIGUSR1,
            SIGUSR2,
        ));

        // Création socket
        #$this->socket = socket_create_listen(80);

        if (($this->socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP)) == false) {
            echo "socket_create_listen() a échoué : " . socket_strerror(socket_last_error($this->socket)) . "\n";
            exit(1);
        }
        // Modification de l'option SO_REUSEADDR à la valeur 1 !
        if (!socket_set_option($this->socket, SOL_SOCKET, SO_REUSEADDR, 1)) {
            echo 'Impossible de définir l\'option du socket : ' . socket_strerror(socket_last_error($this->socket)) . "\n";
            exit(1);
        }
        if (socket_bind($this->socket, "0.0.0.0", 1234) == false) {
            echo "socket_bind() a échoué : " . socket_strerror(socket_last_error($this->socket)) . "\n";
            exit(1);
        }
        if (socket_listen($this->socket) == false) {
            echo "socket_listen() a échoué : " . socket_strerror(socket_last_error($this->socket)) . "\n";
            exit(1);
        }

        socket_set_nonblock($this->socket);

        // Démarrage du démon
        parent::start();
    }

    public function run()
    {
        if ($c = socket_accept($this->socket)) {
            // Passage en mode non bloquant de la socket du client
            socket_set_nonblock($c);
            // Ajout de la socket cliente au tableau
            $this->clients[] = $c;
            echo "Nouveau client !";
        }

        // On répond au clients qui ont envoyés un message
        for ($i = 0; $i < sizeof($this->clients); $i++) {
            $c = $this->clients[$i];

            $buf = socket_read($c, 2048);

            if ($buf === "") {
                echo "Le client $i est parti !";
                array_splice($this->clients, $i, 1);
            } else {

                if (strpos($buf, "zip:") !== false) {
                    $this->size_to_read = substr($buf, 4);
                    echo "Je dois lire : " . $this->size_to_read . " bits";
                }

                // File sending
                if ($this->size_to_read > $this->read) {
                    echo "Add dans le fichier : " . strlen($buf);
                    $file = fopen("output.zip", "ab");
                    fwrite($file, $buf);
                    fclose($file);

                    $this->read += strlen($buf);
                } else {
                    echo $buf;
                }

            }
        }

        $this->str .= fgets(STDIN, 1024);

        // Si on a écrit quelque chose
        if ($this->str != "") {
            $this->parseInput($this->str);
            $this->str = "";
        }

    }

    public function onStart()
    {
        echo "Démarrage du processus avec le pid " . getmypid() . "\n";
    }
    public function onStop()
    {
        echo "Arrêt du processus avec le pid " . getmypid() . "\n";
        socket_close($this->socket);
    }

    public function handleOtherSignals($signal)
    {
        echo "Signal non géré par la classe Daemon : " . $signal . "\n";
    }

    public function parseInput($input)
    {
        $input = preg_replace('~[\r\n]+~', '', $input);

        // Affichage des clients dispo
        if ($input == "clients") {
            foreach ($this->clients as $key => $client) {
                socket_getsockname($client, $IP, $PORT);

                echo "[$key] $IP : $PORT\n";
            }
        } else {
            $pattern = "/(\d*):(.*)/";
            preg_match($pattern, $input, $results);

            if (isset($results[1]) && isset($results[2])) {
                if ($results[1] < count($this->clients)) {
                    socket_write($this->clients[$results[1]], $results[2]);
                } else {
                    echo 'Client inconnu !';
                }
            }

        }

        echo "\n";
    }
}
