<?php
/* Copyright (c) 2020 xxAROX. All rights reserved. */
namespace xxAROX\LanguageAPI\utils;

/**
 * Class Time
 * @package xxAROX\LanguageAPI\utils
 * @author xxAROX
 * @date 02.04.2020 - 23:13
 * @project TrollSystem
 */
class Time
{
	/** @var float */
	private $time;


	/**
	 * Time constructor.
	 * @param null $time
	 */
	public function __construct($time=NULL){
		if (is_null($time)) {
			$this->time = (float)microtime(TRUE);
		} else if (is_float($time)) {
			$this->time = $time;
		}
	}

	/**
	 * Function now
	 * @return Time
	 */
	public static function now(): Time{
		return new Time();
	}

	/**
	 * Function getTimeSecs
	 * @return int
	 */
	public function getTimeSecs(): int{
		return floor($this->time);
	}

	/**
	 * Function getTimeMillis
	 * @param bool $getAsFloat
	 * @return int
	 */
	public function getTimeMillis(bool $getAsFloat = FALSE): int{
		if ($getAsFloat) {
			return $this->time;
		}
		return (int)($this->time *1000);
	}

	/**
	 * Function getElapsedSecs
	 * @param Time $time
	 * @return Time
	 */
	public function getElapsedSecs(Time $time): Time{
		return new Time($this->getTimeSecs() - $time->getTimeSecs());
	}

	/**
	 * Function getElapsedMillis
	 * @param Time $time
	 * @return Time
	 */
	public function getElapsedMillis(Time $time): Time{
		return new Time($this->getTimeMillis() - $time->getTimeMillis());
	}

	/**
	 * Function diffSecs
	 * @param Time $time
	 * @return float|int
	 */
	public function diffSecs(Time $time){
		return abs($this->getTimeSecs() - $time->getTimeSecs());
	}

	/**
	 * Function diffMillis
	 * @param Time $time
	 * @return float|int
	 */
	public function diffMillis(Time $time){
		return abs($this->getTimeMillis() - $time->getTimeMillis());
	}
}
