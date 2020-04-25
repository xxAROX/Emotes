<?php
/* Copyright (c) 2020 xxAROX. All rights reserved. */
namespace xxAROX\Emotes;
use pocketmine\plugin\PluginBase;


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
		$this->getServer()->getPluginManager()->registerEvents(new EventListener(), $this);
	}

	public function onDisable(): void{
	}

	public function getPrefix(): string{
		return $this->prefix;
	}

	public static function getInstance(): self{
		return self::$instance;
	}
}
