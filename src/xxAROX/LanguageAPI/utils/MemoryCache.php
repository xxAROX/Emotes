<?php
/* Copyright (c) 2020 xxAROX. All rights reserved. */
namespace xxAROX\LanguageAPI\utils;

use pocketmine\utils\Binary;


/**
 * Class MemoryCache
 * @package xxAROX\LanguageAPI\utils
 * @author xxAROX
 * @date 02.04.2020 - 22:55
 * @project TrollSystem
 */
class MemoryCache
{
	const OVERRIDE_DYNAMIC = 0;
	const OVERRIDE_CREATE = 1;
	const OVERRIDE_OPEN = 2;

	/** @var int */
	private $memoryAddressMain=0, $memoryAddressSize=0, $memoryAddressUpdate=0;
	/**@var resource */
	private $shmrMain, $shmrSize, $shmrUpdate;


	/**
	 * MemoryCache constructor.
	 * @param int $memAddress
	 * @param int $override
	 */
	public function __construct(int $memAddress, int $override = self::OVERRIDE_DYNAMIC){
		$this->memoryAddressMain = $memAddress;
		$this->memoryAddressUpdate = ++$memAddress;
		$this->memoryAddressSize = ++$memAddress;

		$shmr = FALSE;
		$shmrSize = FALSE;
		$shmrUpdate = FALSE;

		switch ($override) {
			case self::OVERRIDE_DYNAMIC:
				$shmrSize = self::dynamicCreate($this->memoryAddressSize, 0644, 4);
				$shmrUpdate = self::dynamicCreate($this->memoryAddressUpdate, 0644, 4);
				break;
			case self::OVERRIDE_CREATE:
			case self::OVERRIDE_OPEN:
				// TODO: Maybe implement these overrides, currently they're not needed
				break;
		}
		$this->shmrMain = $shmr;
		$this->shmrSize = $shmrSize;
		$this->shmrUpdate = $shmrUpdate;

		if ($this->isInitiated()) {
			$this->plug();
		}
	}

	/**
	 * Function getLastUpdated
	 * @return Time
	 */
	public function getLastUpdated(): Time{
		return new Time((float)Binary::readInt(shmop_read($this->shmrUpdate, 0, 4)));
	}

	/**
	 * Function update
	 * @return void
	 */
	public function update(): void{
		shmop_write($this->shmrUpdate, Binary::writeInt(time()), 0);
	}

	/**
	 * @return int
	 */
	public function getSize(): int{
		return Binary::readInt(shmop_read($this->shmrSize, 0, 4));
	}

	/**
	 * @param int $size
	 * You probably won't needs this.
	 * Please know what you're doing before using this function externally
	 */
	public function setSize(int $size): void{
		shmop_write($this->shmrSize, Binary::writeInt($size), 0);
	}

	/**
	 * @param string $data
	 */
	public function setData(string $data): void{
		$this->update();
		$size = strlen($data);

		if ($size > 2147483647) {
			throw new \RuntimeException("Data cannot be written to Shared Memory because its too big. Limit: 2147.483647 MB (2147483647 bytes)");
		}
		$this->setSize($size);
		$this->allocateMemory($size);
		$this->rawWriteData($data);
	}

	/**
	 * @return string
	 */
	public function getData(): string{
		try {
			return shmop_read($this->shmrMain, 0, $this->getSize());
		} catch (\ErrorException $e) {
			return "null";
		}
	}

	/**
	 * Function isInitiated
	 * @return bool
	 */
	public function isInitiated(): bool{
		try {
			shmop_open($this->memoryAddressMain, 'w', 0644, 1);
		} catch (\ErrorException $e) {
			return FALSE;
		}
		return TRUE;
	}

	/**
	 * Function rawWriteData
	 * @param string $data
	 * @param int $offset
	 *
	 * Exactly like `memoryCache::setData()` but *ONLY* writes the Data.
	 * `memoryCache::setData()` also updates the Size and allocates Memory, this one doesn't.
	 *
	 * You probably won't needs this.
	 * Please know what you're doing before using this function externally
	 *
	 * @return void
	 */
	public function rawWriteData(string $data, int $offset = 0): void{
		shmop_write($this->shmrMain, $data, $offset);
	}

	/**
	 * Function allocateMemory
	 * @param int $allocationSize
	 * Can also reallocate
	 * Dont use this if you don't know how to use it
	 * @return void
	 */
	public function allocateMemory(int $allocationSize): void{
		if (self::isAddressInUse($this->memoryAddressMain)) {
			$dynres = shmop_open($this->memoryAddressMain, 'w', 0644, $allocationSize);
			shmop_delete($dynres);
		}
		$this->shmrMain = shmop_open($this->memoryAddressMain, 'c', 0644, $allocationSize);
		$this->plugMemory($allocationSize);
	}

	public function plugMemory(int $size, string $mode = 'w'): void{
		$this->shmrMain = shmop_open($this->memoryAddressMain, $mode, 0644, $size);
	}

	/**
	 * Function plug
	 * Initiates everything to be able to read.
	 * Wont work when there is no data.
	 * @return void
	 */
	public function plug(): void{
		$size = $this->getSize();
		$this->plugMemory($size);
	}

	/**
	 * Function deallocate
	 * @return void
	 */
	public function deallocate(): void{
		@shmop_delete($this->shmrSize);
		@shmop_delete($this->shmrUpdate);
		@shmop_delete($this->shmrMain);
	}

	/**
	 * Function isAddressInUse
	 * @param int $address
	 * @return bool
	 */
	public static function isAddressInUse(int $address): bool{
		try {
			shmop_open($address, 'a', 0664, 1);
		} catch (\ErrorException $e) {
			return FALSE;
		}
		return TRUE;
	}

	/**
	 * Function dynamicCreate
	 * @param int $address
	 * @param int $mode
	 * @param int $size
	 * @return resource
	 */
	public static function dynamicCreate(int $address, int $mode, int $size){
		/*
		if(self::isAddressInUse($address)) {
			$res = shmop_open($address, 'w', $mode, $size);
		} else {
			shmop_open($address, 'n', $mode, $size);
			$res = shmop_open($address, 'w', $mode, $size);
		}
		*/
		return shmop_open($address, 'c', $mode, $size);
	}

}
