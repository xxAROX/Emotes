<?php
/* Copyright (c) 2020 xxAROX. All rights reserved. */
namespace xxAROX\Emotes;
use pocketmine\plugin\PluginBase;
use xxAROX\LanguageAPI\LanguageAPI;


/**
 * Class Main
 * @package xxAROX\Emotes
 * @author xxAROX
 * @date 25.04.2020 - 21:35
 * @project Emotes
 */
class Main extends PluginBase
{
	private static $instance;
	const PREFIX = "§eStimoMC §8» §7";
	private $prefix = self::PREFIX;


	public function onLoad(): void{
		self::$instance = $this;
	}

	public function onEnable(): void{
		$this->registerAddons();
		$this->registerCommands();
		$this->registerListeners();
		$this->registerTasks();
	}

	public function onDisable(): void{
	}

	public function getPrefix(): string{
		return $this->prefix;
	}

	public static function getInstance(): self{
		return self::$instance;
	}

	/**
	 * Function registerListeners
	 * @return void
	 */
	private function registerListeners(): void{
		$pluginManager = $this->getServer()->getPluginManager();

		$this->getLogger()->debug("registered listeners.");
	}

	/**
	 * Function registerCommands
	 * @return void
	 */
	private function registerCommands(): void{
		$this->getServer()->getCommandMap()->registerAll("EMOTES", [
		]);
		$this->getLogger()->debug("registered commands.");
	}

	/**
	 * Function registerTasks
	 * @return void
	 */
	private function registerTasks(): void{
		$scheduler = $this->getScheduler();

		$this->getLogger()->debug("registered tasks.");
	}

	/**
	 * Function registerAddons
	 * @return void
	 */
	private function registerAddons(): void{
		if (!LanguageAPI::isRegistered())
			LanguageAPI::register($this);

		$this->getLogger()->debug("registered addons.");
	}
}
