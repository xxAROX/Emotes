<?php
/* Copyright (c) 2020 xxAROX. All rights reserved. */
namespace xxAROX\Emotes\task;
use pocketmine\Player;
use pocketmine\scheduler\Task;
use xxAROX\Emotes\Main;


/**
 * Class ResetSkinTask
 * @package xxAROX\Emotes\task
 * @author xxAROX
 * @date 25.04.2020 - 22:56
 * @project Emotes
 */
class ResetSkinTask extends Task{
	protected $player;


	/**
	 * ResetSkinTask constructor.
	 * @param Player $player
	 */
	public function __construct(Player $player){
		$this->player = $player;
	}

	/**
	 * Function onRun
	 * @param int $currentTick
	 * @return void
	 */
	public function onRun(int $currentTick){
		if ($this->player instanceof Player) {
			$this->player->setSkin(Main::$skins[$this->player->getName()]);
			$this->player->sendSkin($this->player->getViewers());
		}
	}
}
