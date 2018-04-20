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
		echo "On tourne !\n";

		$this -> socketConnexion();

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
		if(@socket_bind ( $socket, "127.0.0.1", 1234 )==FALSE){
			echo "socket_bind() a échoué : " . socket_strerror ( socket_last_error ($socket) ) . "\n";
			exit ( 1 );
		}
		if(socket_listen ( $socket )==FALSE){
			echo "socket_listen() a échoué : " . socket_strerror ( socket_last_error ($socket) ) . "\n";
			exit ( 1 );
		}
		while ( $c = socket_accept ( $socket ) ) {
			/* Traiter la requête entrante */
		}
		socket_close ( $socket );
	}
}
?>
