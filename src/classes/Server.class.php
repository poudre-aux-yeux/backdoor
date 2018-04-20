<?php
include_once("Daemon.class.php");

class Server extends Daemon {
	public function __construct() {
		// Ici on souhaite gérer les signaux SIGUSR1 et SIGUSR2 en plus
		parent::__construct ( "server", array (
				SIGUSR1,
				SIGUSR2 
		) );
		// Démarrage du démon
		parent::start ();
	}

	public function run() {
		// Le code qui s'exécute infiniment
		echo "On tourne autour du pot !\n";
		// $this -> socketConnexion();
		sleep ( 5 );
	}

	public function onStart() {
		echo "Démarrage du processus avec le pid " . getmypid () . "\n";
	}
	public function onStop() { 
		echo "Arrêt du processus avec le pid " . getmypid () . "\n";
	}
	
	public function handleOtherSignals($signal) {
		echo "Signal non géré par la classe Daemon : " . $signal . "\n";
	}

	public function socketConnexion() {
		if (($socket = socket_create ( AF_INET, SOCK_STREAM, SOL_TCP )) == FALSE) {
			echo "socket_create_listen() a échoué : " . socket_strerror ( socket_last_error ($socket) ) . "\n";
			exit ( 1 );
		}
		// Modification de l'option SO_REUSEADDR à la valeur 1 !
		if (!socket_set_option($socket, SOL_SOCKET, SO_REUSEADDR, 1)) {
			echo 'Impossible de définir l\'option du socket : '. socket_strerror(socket_last_error($socket)) . "\n";
			exit ( 1 );
		}
		if(socket_bind ( $socket, "127.0.0.1", 1234 )==FALSE){
			echo "socket_bind() a échoué : " . socket_strerror ( socket_last_error ($socket) ) . "\n";
			exit ( 1 );
		}
		if(socket_listen ( $socket )==FALSE){
			echo "socket_listen() a échoué : " . socket_strerror ( socket_last_error ($socket) ) . "\n";
			exit ( 1 );
		}
		// Passage en mode non bloquant de la socket du serveur
		socket_set_nonblock ( $socket );
		$clients = array ();
		while ( TRUE ) {
			if ($c = socket_accept ( $socket )) {
				// Passage en mode non bloquant de la socket du client
				socket_set_nonblock ( $c );
				// Ajout de la socket cliente au tableau
				$clients [] = $c;
			}
			// On répond au clients qui ont envoyés un message
			for($i = 0; $i < sizeof ( $clients ); $i ++) {
				$c = $clients [$i];
				if ($buf = socket_read ( $c, 2048 )) {
					socket_write ( $c, "You said : " . $buf );
				}
			}
			// On efface les sockets fermées
			for($i = 0; $i < sizeof ( $clients ); $i ++) {
				$c = $clients [$i];
				if ($c == FALSE) {
					$clients = array_splice ( $clients, $i, 1 );
				}
			}
		usleep(500);
		}
		socket_close ( $socket );
	}
}
?>