<?php
/**
 * @package kernel
 * @author spiderr <spiderr@bitweaver.org>
 * Copyright (c) 2025 bitweaver.org, All Rights Reserved
 * This source file is subject to the 2.0 GNU GENERAL PUBLIC LICENSE. 
 *
 * BitCliArgs: Simplified CLI Argument Processing Library
 * Supports long options, defaults, and help output
 */

/**
 * Class to manage CLI arguments
 */
class BitCliArgs {
	private $options = [];
	private $descriptions = [];
	private $defaults = [];
	private $scriptName;

	/**
	 * Initialize with script name
	 * @param string $scriptName Name of the script for usage output
	 */
	public function __construct(string $scriptName) {
		$this->scriptName = basename($scriptName);
	}

	/**
	 * Add an option with long name, description, and optional default value
	 * @param string $long Long option (e.g., 'help')
	 * @param string $description Description for help output
	 * @param mixed $default Default value (null if none)
	 * @param bool $required Whether the option requires a value
	 */
	public function addOption(string $long, string $description, $default = null, bool $required = false) {
		$this->options[$long] = $required ? "$long:" : "$long::";
		$this->descriptions[$long] = $description;
		$this->defaults[$long] = $default;
	}

	/**
	 * Parse command-line arguments
	 * @return array Parsed arguments with defaults applied
	 */
	public function parse(): array {
		$longOpts = array_values($this->options);
		$opts = getopt('', $longOpts);

		// Apply defaults for unset options
		$result = $opts;
		foreach ($this->defaults as $long => $default) {
			if (!isset($result[$long])) {
				$result[$long] = $default;
			}
		}

		// Handle help option
		if (isset($result['help'])) {
			$this->printHelp();
			exit(0);
		}

		return $result;
	}

	/**
	 * Print usage information
	 */
	private function printHelp() {
		echo "Usage: php {$this->scriptName} [options]\n\n";
		echo "Options:\n";
		foreach ($this->options as $long => $format) {
			$optStr = "  --$long";
			if (substr($format, -1) === ':') {
				$optStr .= " <value>";
			}
			$default = $this->defaults[$long] !== null ? "(default: {$this->defaults[$long]})" : '';
			echo sprintf("%-20s %s %s\n", $optStr, $this->descriptions[$long], $default);
		}
		echo "\n";
	}

	/**
	 * Get environment variables for passing to child processes
	 * @return array Environment variables from $_SERVER
	 */
	public function getEnvVars(): array {
		$envVars = [];
		foreach ($_SERVER as $key => $value) {
			// Filter out PHP-specific $_SERVER keys
			if (!in_array($key, ['argc', 'argv', 'SCRIPT_NAME', 'PHP_SELF', 'REQUEST_TIME', 'DOCUMENT_ROOT', 'SERVER_SOFTWARE'])) {
				if (is_string($value)) {
					$envVars[$key] = $value;
				}
			}
		}
		return $envVars;
	}

	/**
	 * Get all raw command-line arguments (including undefined ones)
	 * @return array Raw arguments from $argv
	 */
	public function getRawArgs(): array {
		global $argv;
		return array_slice($argv, 1); // Skip script name
	}
}

