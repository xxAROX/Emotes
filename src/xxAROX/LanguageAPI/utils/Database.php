<?php
/* Copyright (c) 2020 xxAROX. All rights reserved. */
namespace xxAROX\LanguageAPI\utils;
use pocketmine\plugin\Plugin;
use xxAROX\LanguageAPI\LanguageAPI;


/**
 * Class Database
 * @package xxAROX\LanguageAPI\utils
 * @author xxAROX
 * @date 16.03.2020 - 19:06
 */
class Database
{
	/** @var Medoo */
	private $medoo = NULL;
	/** @var Plugin */
	private $plugin = NULL;


	/**
	 * Database constructor.
	 * @param Plugin $plugin
	 */
	public function __construct(Plugin $plugin){
		$this->plugin = $plugin;
		$config = LanguageAPI::getInstance()->getGlobalProperties();
		$db = $config->get("Database");

		$this->medoo = new Medoo([
			"database_type" => "mysql",
			"database_name" => $db["name"],
			"server"        => $db["address"],
			"port"          => $db["port"] ?? 3306,
			"username"      => $db["username"],
			"password"      => $db["password"]
		]);

		if (!$this->isTableInitialized()) {
			$this->getPlugin()->getLogger()->warning("MYSQL TABLE ERROR.");
			$this->getPlugin()->setEnabled(FALSE);
			return;
		}
	}

	/**
	 * Function getMedoo
	 * @return Medoo
	 */
	public function getMedoo(): Medoo{
		return $this->medoo;
	}

	/**
	 * Function getPlugin
	 * @return Plugin
	 */
	private function getPlugin(): Plugin{
		return $this->plugin ?? LanguageAPI::getInstance()->getPlugin();
	}

	/**
	 * Function isTableInitialized
	 * @return bool
	 */
	public function isTableInitialized(): bool{
		$query = $this->getMedoo()->query('SELECT 1 FROM users LIMIT 1;')->errorCode();
		if ($query == "00000") {
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * Function updateLanguage
	 * @param string $playerName
	 * @param string $lang
	 * @return void
	 */
	public function updateLanguage(string $playerName, string $lang): void{
		$this->getMedoo()->update("users", ["lang" => $lang], ["name" => $playerName]);
	}

	/**
	 * Function getLanguage
	 * @param string $playerName
	 * @return string
	 */
	public function getLocale(string $playerName): string{
		return $this->getMedoo()->get("users", "lang", ["name" => $playerName]) ?? "en_us";
	}
}
