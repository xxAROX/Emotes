<?php
/* Copyright (c) 2020 xxAROX. All rights reserved. */
namespace xxAROX\Emotes;
use pocketmine\entity\Skin;
use pocketmine\Player;
use xxAROX\Emotes\event\PlayerEmoteEvent;
use xxAROX\Emotes\task\ResetSkinTask;
use xxAROX\LanguageAPI\LanguageAPI;


/**
 * Class Emote
 * @package xxAROX\Emotes
 * @author xxAROX
 * @date 25.04.2020 - 22:03
 * @project Emotes
 */
class Emote{
	protected $name;
	protected $path;
	protected $permission;


	/**
	 * Emote constructor.
	 * @param string $name
	 * @param string $path
	 * @param string|null $permission
	 */
	public function __construct(string $name, string $path, ?string $permission=NULL){
		$this->name = $name;
		$this->path = $path;
		$this->permission = $permission;
	}

	/**
	 * Function getPlugin
	 * @return Main
	 */
	private function getPlugin(): Main{
		return Main::getInstance();
	}

	/**
	 * Function getName
	 * @return string
	 */
	public function getName(): string{
		return $this->name;
	}

	/**
	 * Function getPath
	 * @return string
	 */
	public function getPath(): string{
		return $this->path;
	}

	/**
	 * Function getPermission
	 * @return string|null
	 */
	public function getPermission(): ?string{
		return $this->permission;
	}

	/**
	 * Function sendTo
	 * @param Player $player
	 * @return void
	 */
	public function sendTo(Player $player): void{
		if (!is_null($this->permission)) {
			if (!$player->hasPermission($this->permission)) {
				LanguageAPI::sendMessage($player, "message.noPermission", [$this->permission]); //NOTE: LanguageAPI used.
				return;
			}
		}
		$ev = new PlayerEmoteEvent($player, $this);
		$ev->call();

		if (!$ev->isCancelled()) {
			$player->setSkin(new Skin($player->getSkin()->getSkinId(), $this->getPlugin()->getUtils()->mergeSkin($player->getSkin(), $this), $player->getSkin()->getCapeData(), $player->getSkin()->getGeometryName(), $player->getSkin()->getGeometryData()));
			$player->sendSkin($player->getViewers());
			$this->getPlugin()->getScheduler()->scheduleDelayedTask(new ResetSkinTask($player), 20 * 5);
		}
	}

	/**
	 * Function getSkinData
	 * @param int|null $resolution
	 * @return string
	 */
	public function getSkinData(?int $resolution=64): string{
		return $this->getPlugin()->getUtils()->toSkinData($this->path . "/{$resolution}/{$this->name}.png");
	}
}
