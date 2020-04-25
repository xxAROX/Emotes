<?php
/* Copyright (c) 2020 xxAROX. All rights reserved. */
namespace xxAROX\Emotes\listener;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use xxAROX\Emotes\Main;


/**
 * Class JoinListener
 * @package xxAROX\Emotes\listener
 * @author xxAROX
 * @date 25.04.2020 - 22:51
 * @project Emotes
 */
class JoinListener implements Listener{
	/**
	 * Function onJoin
	 * @param PlayerJoinEvent $event
	 * @return void
	 */
	public function onJoin(PlayerJoinEvent $event): void{
		Main::$skins[$event->getPlayer()->getName()] = $event->getPlayer()->getSkin();
	}
}
