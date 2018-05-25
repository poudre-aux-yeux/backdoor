<?php
abstract class Daemon {
	protected $name;
	private $isRunning = true;
	private $signals = array (
			SIGTERM,
			SIGINT,
			SIGCHLD,
			SIGHUP 
	);
	/**
	 * Class used to handle POSIX signals and fork from the current process
	 *
	 * @param string $name
	 *        	<p>The name of the class</p>
	 * @param array $signals
	 *        	<p>An array containing additional POSIX signals to handle [optionel] </p>
	 */
	protected function __construct($name, array $signals = array()) {
		$this->name = $name;
		if (! empty ( $signals )) {
			$this->signals = array_merge ( $this->signals, $signals );
		}
		// Permet au script PHP de s'éxécuter indéfiniment
		set_time_limit ( 0 );
		$this->registerSignals ();
	}
	/**
	 * Used to register POSIX signals
	 */
	private function registerSignals() {
		declare ( ticks = 1 )
			;
		foreach ( $this->signals as $signal ) {
			@pcntl_signal ( $signal, array (
					'Daemon',
					'handleSignal' 
			) );
		}
	}
	/**
	 * Used to handle properly SIGINT, SIGTERM, SIGCHLD and SIGHUP
	 *
	 * @param string $signal        	
	 */
	protected function handleSignal($signal) {
		if ($signal == SIGTERM || $signal == SIGINT) {
			// Gestion de l'extinction
			$this->isRunning = false;
		} else if ($signal == SIGHUP) {
			// Gestion du redémarrage
			$this->onStop ();
			$this->onStart ();
		} else if ($signal == SIGCHLD) {
			// Gestion des processus fils
			pcntl_waitpid ( - 1, $status, WNOHANG );
		} else {
			// Gestion des autres signaux
			$this->handleOtherSignals ( $signal );
		}
	}
	/**
	 * Launch the infinite loop executing the ''run'' abstract method
	 */
	protected function start() {
		$this->onStart ();
		while ( $this->isRunning ) {
			$this->run ();
		}
		$this->onStop ();
	}
	/**
	 * True if the daemon is running
	 */
	public function isRunning(){
		return $this->isRunning;
	}
	/**
	 * Override to implement the code run infinetly
	 */
	protected abstract function run();
	/**
	 * Override to execute code before the ''run'' method on daemon start
	 */
	protected abstract function onStart();
	/**
	 * Override to execute code after the ''run'' method on daemon shutdown
	 */
	protected abstract function onStop();
	/**
	 * Override to handle additional POSIX signals
	 *
	 * @param int $signal
	 *        	<p>Signal sent by interrupt</p>
	 */
	protected abstract function handleOtherSignals($signal);
}

?>