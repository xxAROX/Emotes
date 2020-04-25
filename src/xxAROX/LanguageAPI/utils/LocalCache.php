<?php
/* Copyright (c) 2020 xxAROX. All rights reserved. */
namespace xxAROX\LanguageAPI\utils;
/**
 * Class LocalCache
 * @package xxAROX\LanguageAPI\utils
 * @author xxAROX
 * @date 02.04.2020 - 22:52
 * @project TrollSystem
 */
class LocalCache
{
	private $data;
	private $lastUpdate;


	/**
	 * LocalCache constructor.
	 */
	public function __construct(){
		$this->lastUpdate = new Time(0.0);
	}

	/**
	 * Function setData
	 * @param string $data
	 * @return void
	 */
	public function setData(string $data): void {
		$this->lastUpdate = (float)microtime(true);
		$this->data = $data;
	}

	/**
	 * Function getData
	 * @return string
	 */
	public function getData(): string{
		return $this->data;
	}

	/**
	 * Function getLastUpdated
	 * @return Time
	 */
	public function getLastUpdated(): Time{
		return $this->lastUpdate;
	}

	/**
	 * Function getSize
	 * @return int
	 */
	public function getSize(): int{
		return strlen($this->data);
	}
}
