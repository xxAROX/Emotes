<?php
/* Copyright (c) 2020 xxAROX. All rights reserved. */
namespace xxAROX\LanguageAPI;
use pocketmine\command\CommandSender;
use pocketmine\permission\Permission;
use pocketmine\permission\PermissionManager;
use pocketmine\Player;
use pocketmine\plugin\Plugin;
use pocketmine\Server;
use pocketmine\utils\Config;
use xxAROX\LanguageAPI\command\LanguageCommand;
use xxAROX\LanguageAPI\task\FetchLanguageAsyncTask;
use xxAROX\LanguageAPI\utils\Database;
use xxAROX\LanguageAPI\utils\Language;
use xxAROX\LanguageAPI\utils\LocalCache;
use xxAROX\LanguageAPI\utils\MemoryCache;


/**
 * Class LanguageAPI
 * @package xxAROX\LanguageAPI
 * @author xxAROX
 * @date 28.03.2020 - 19:58
 * @project GetDown
 */
class LanguageAPI
{
	public static $registered = FALSE;
	const LANGUAGE_URL = "https://raw.githubusercontent.com/StimoMC/lang/master/";
	#public $cache = [];
	protected $plugin;
	private static $instance;

	private static $useLocalCache = FALSE;


	public $cache;
	/** @var array */
	public $internCache;
	public $internCacheUpdate = TRUE;


	/**
	 * LanguageAPI constructor.
	 * @param Plugin $plugin
	 */
	public function __construct(Plugin $plugin){
		self::$instance = $this;
		self::$registered = TRUE;
		$this->plugin = $plugin;

		PermissionManager::getInstance()->addPermission(new Permission("xxarox.command.language.reload", "Allows to reload the LanguageAPI.", "op", []));
		$plugin->getLogger()->info("§dLanguageAPI §bwas registered.");

		if (self::$useLocalCache) {
			$this->cache = new LocalCache();
			$plugin->getLogger()->warning("Using local cache. It is recommended to refrain from using local cache.");
			$plugin->getLogger()->warning("Cache saved in shared memory segments is much more controllable, space efficient and is faster with multiple servers.");
		} else {
			$oldAddress = 231147;
			$this->cache = new MemoryCache(489613);
		}
		new Database($plugin);
		$plugin->getServer()->getCommandMap()->registerAll("LANGUAGEAPI", [new LanguageCommand("language", $this)]);
		$this->reload();
	}

	/**
	 * Function getRawData
	 * @return string
	 */
	public function getRawData(): string{
		return $this->cache->getData();
	}

	/**
	 * Function getData
	 * @return array
	 */
	public function getData(): array{
		if (!$this->internCacheUpdate) {
			return $this->internCache;
		}
		$fresh = (array)igbinary_unserialize($this->cache->getData());
		$this->internCache = $fresh;
		$this->internCacheUpdate = FALSE;
		return $fresh;
	}

	/**
	 * Function getCacheContent
	 * @return array
	 * @deprecated
	 */
	public function getCacheContent(): array{
		return $this->getData();
	}

	/**
	 * Function getLanguages
	 * @return Language[]
	 */
	public function getLanguages(): iterable{
		foreach ($this->getCacheContent() as $lang) {
			yield new Language($lang);
		}
	}

	/**
	 * Function getAvailableLocals
	 * @return string[]
	 */
	public function getAvailableLocals(): iterable{
		foreach ($this->getCacheContent() as $lang) {
			yield $lang["localeCode"];
		}
	}

	/**
	 * Function getAvailableNames
	 * @return string[]
	 */
	public function getAvailableNames(): iterable{
		foreach ($this->getCacheContent() as $lang) {
			yield $lang["name"];
		}
	}

	/**
	 * Function needsReload
	 * @return bool
	 */
	public function needsReload(): bool{
		return $this->getCacheContent() === NULL;
	}

	/**
	 * Function reload
	 * @param bool|null $force
	 * @return void
	 */
	public function reload(?bool $force = FALSE): void{
		if (!$force && $this->cache->getLastUpdated()->getTimeSecs() > 0) {
			Server::getInstance()->getLogger()->warning("Language reload was requested, but rejected.");
			return;
		}
		Server::getInstance()->getLogger()->notice("Reloading Language...");
		Server::getInstance()->getAsyncPool()->submitTask(new FetchLanguageAsyncTask(LanguageAPI::getInstance()->getGlobalProperties()->getNested("Language.languages", ["en_US"])));
	}

	/**
	 * Function isRegistered
	 * @return bool
	 */
	public static function isRegistered(): bool{
		return self::$registered;
	}

	/**
	 * Function register
	 * @param Plugin $plugin
	 * @return void
	 */
	public static function register(Plugin $plugin): void{
		if (self::$registered) {
			$plugin->getLogger()->info("§cLanguageAPI is already registered.");
		} else {
			new self($plugin);
		}
	}

	/**
	 * Function getInstance
	 * @return LanguageAPI
	 */
	public static function getInstance(): LanguageAPI{
		return self::$instance;
	}

	/**
	 * Function getPlugin
	 * @return Plugin
	 */
	public function getPlugin(): Plugin{
		return $this->plugin;
	}

	/**
	 * Function getDatabase
	 * @return Database
	 */
	private static function getDatabase(): Database{
		return new Database(self::getInstance()->getPlugin());
	}

	/**
	 * Function getGlobalProperties
	 * @return Config
	 */
	public function getGlobalProperties(): Config{
		return new Config("/home/.global-properties.json", Config::JSON);
	}

	/**
	 * Function updateLanguage
	 * @param string $playerName
	 * @param string $lang
	 * @return void
	 */
	public static function updateLanguage(string $playerName, string $lang): void{
		self::getDatabase()->updateLanguage($playerName, $lang);
	}

	/**
	 * Function getFallbackLanguage
	 * @return Language
	 */
	public static function getFallbackLanguage(): Language{
		$c = self::getInstance()->getCacheContent();
		return new Language($c["en_US"]);
	}

	/**
	 * Function getLanguage
	 * @param string $lang
	 * @return Language
	 */
	public static function getLanguage(string $lang): Language{
		$c = self::getInstance()->getCacheContent();
		if (!isset($c[$lang])) {
			return self::getFallbackLanguage();
		}
		return new Language($c[$lang]);
	}

	/**
	 * Function getLanguageByPlayer
	 * @param Player $player
	 * @return Language
	 */
	public static function getLanguageByPlayer(Player $player): Language{
		$lang = self::getDatabase()->getLocale($player->getName());
		return self::getLanguage($lang);
	}

	/**
	 * Function translate
	 * @param $player
	 * @param string $str
	 * @param array $params
	 * @return string
	 */
	public static function translate($player, string $str, ?array $params = []): string{
		$lang = $player instanceof Player
			? self::getLanguage(self::getDatabase()->getLocale($player->getName()))
			: $lang = self::getInstance()->getFallbackLanguage();

		return $lang->translate($str, $params);
	}

	/**
	 * Function sendMessage
	 * @param $player
	 * @param $message
	 * @param array $params
	 * @return void
	 */
	public static function sendMessage($player, $message, ?array $params = []): void{
		$lang = $player instanceof Player
			? self::getLanguage(self::getDatabase()->getLocale($player->getName()))
			: $lang = self::getFallbackLanguage();

		$player->sendMessage($lang->translate($message, $params));
	}

	/**
	 * Function sendLoadedLanguages
	 * @param CommandSender $sender
	 * @return void
	 */
	public function sendLoadedLanguages(CommandSender $sender): void{
		$languages = [];
		
		if ($this->getData()["en_US"] == NULL) {
			Server::getInstance()->getLogger()->alert("Language reload cancelled.");
			return;
		}

		foreach ($this->getLanguages() as $language) {
			$languages[] = $language->getName();
		}
		$sender->sendMessage("§7Following languages loaded: §e" . join("§7, §e", $languages) . "§7.");
	}
}
