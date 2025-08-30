<?php
/**
 * @package kernel
 * @author spiderr <spiderr@bitweaver.org>
 * Copyright (c) 2025 bitweaver.org, All Rights Reserved
 * This source file is subject to the 2.0 GNU GENERAL PUBLIC LICENSE. 
 *
 * BitLogger: Simple logging library for PHP CLI applications
 * Supports PSR-3 log levels and site instance context
 */

/**
 * Class to handle logging with site instance context
 */
class BitLogger {
	private $logFile;
	private $instance;

	// PSR-3 log levels
	const EMERGENCY = 'EMERGENCY';
	const ALERT = 'ALERT';
	const CRITICAL = 'CRITICAL';
	const ERROR = 'ERROR';
	const WARNING = 'WARNING';
	const NOTICE = 'NOTICE';
	const INFO = 'INFO';
	const DEBUG = 'DEBUG';

	/**
	 * Initialize logger with log file path and site instance
	 * @param string $logFile Path to log file
	 * @param string $instance Site instance identifier
	 */
	public function __construct(string $logFile, string $instance = 'global') {
		$this->logFile = $logFile;
		$this->instance = $instance;

		// Ensure log file directory exists
		$dir = dirname($logFile);
		if (!is_dir($dir)) {
			mkdir($dir, 0755, true);
		}
	}

	/**
	 * Log a message with the specified level
	 * @param string $level Log level (e.g., INFO, ERROR)
	 * @param string $message Message to log
	 * @param array $context Additional context (optional)
	 */
	public function log(string $level, string $message, array $context = []) {
		$validLevels = [
			self::EMERGENCY,
			self::ALERT,
			self::CRITICAL,
			self::ERROR,
			self::WARNING,
			self::NOTICE,
			self::INFO,
			self::DEBUG
		];

		// Validate log level
		$level = strtoupper($level);
		if (!in_array($level, $validLevels)) {
			$this->log(self::ERROR, "Invalid log level: $level");
			return;
		}

		// Format log message
		$timestamp = date('Y-m-d H:i:s');
		$contextStr = $context ? ' ' . json_encode($context) : '';
		$logLine = "[$timestamp] [{$this->instance}] [$level] $message$contextStr\n";

		// Write to log file (thread-safe)
		file_put_contents($this->logFile, $logLine, FILE_APPEND | LOCK_EX);
	}

	// Convenience methods for each log level
	public function emergency(string $message, array $context = []) {
		$this->log(self::EMERGENCY, $message, $context);
	}

	public function alert(string $message, array $context = []) {
		$this->log(self::ALERT, $message, $context);
	}

	public function critical(string $message, array $context = []) {
		$this->log(self::CRITICAL, $message, $context);
		exit;
	}

	public function error(string $message, array $context = []) {
		$this->log(self::ERROR, $message, $context);
	}

	public function warning(string $message, array $context = []) {
		$this->log(self::WARNING, $message, $context);
	}

	public function notice(string $message, array $context = []) {
		$this->log(self::NOTICE, $message, $context);
	}

	public function info(string $message, array $context = []) {
		$this->log(self::INFO, $message, $context);
	}

	public function debug(string $message, array $context = []) {
		$this->log(self::DEBUG, $message, $context);
	}
}

/* Example usage
if (php_sapi_name() === 'cli' && basename(__FILE__) === 'logger.php') {
	$logger = new BitLogger('logs/app.log', 'test_instance');
	$logger->info('Application started');
	$logger->warning('Low disk space', ['free_space' => '500MB']);
	$logger->error('Failed to connect to database', ['error_code' => 1001]);
}
*/
