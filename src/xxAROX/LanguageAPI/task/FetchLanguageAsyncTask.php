<?php
/* Copyright (c) 2020 xxAROX. All rights reserved. */
namespace xxAROX\LanguageAPI\task;
use pocketmine\scheduler\AsyncTask;
use pocketmine\Server;
use pocketmine\utils\Internet;
use xxAROX\LanguageAPI\LanguageAPI;


/**
 * Class FetchLanguageAsyncTask
 * @package xxAROX\LanguageAPI\task
 * @author xxAROX
 * @date 29.03.2020 - 07:31
 * @project GetDown
 */
class FetchLanguageAsyncTask extends AsyncTask
{
	protected $languages;


	/**
	 * FetchLanguageAsyncTask constructor.
	 * @param array $languages
	 */
	public function __construct(array $languages){
		$this->languages = $languages;
	}

	/**
	 * Function onRun
	 * @return void
	 */
	public function onRun(){
		$avabileLangs = $this->languages;
		$langs = [];

		foreach ($avabileLangs as $locale) {
			$jsonString = Internet::getURL(LanguageAPI::LANGUAGE_URL . $locale . ".json");
			$langs[$locale] = json_decode($jsonString, TRUE);
		}
		$this->setResult($langs);
	}

	/**
	 * Function onCompletion
	 * @param Server $server
	 * @return void
	 */
	public function onCompletion(Server $server){
		$result = $this->getResult();

		LanguageAPI::getInstance()->cache->setData(igbinary_serialize($result));
		LanguageAPI::getInstance()->internCacheUpdate = TRUE;
		Server::getInstance()->getLogger()->info("Language reload done.");
	}
}
