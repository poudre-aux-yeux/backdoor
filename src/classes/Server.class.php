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
}
?>
