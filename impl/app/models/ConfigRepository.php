<?php
/**
 * Class ConfigRepository.php , Last changed 18.10.16 22:33
 * This file is part of the drones-map
 * Copyright (c) 2017 Lukáš Vlček (http://www.vlceklukas.cz)
 */

namespace App\Models;

use Nette;

/**
 * Class ConfigRepository
 * @package App\Models
 * @author Lukáš Vlček
 */
class ConfigRepository
{
	/** @var array */
	private $data;

	public function __construct(array $data) {
		$this->data = $data;
	}

	public function &__get($name) {
		if (array_key_exists($name, $this->data)) {
			return $this->data[$name];
		}
		try {
			return parent::__get($name);
		} catch (\ErrorException $e) {
			throw new \ErrorException('Parameter "' . $name . '" not found.');
		}
	}
}