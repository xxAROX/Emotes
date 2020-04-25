<?php
/* Copyright (c) 2020 xxAROX. All rights reserved. */
namespace xxAROX\Emotes\event;
use pocketmine\event\Cancellable;
use pocketmine\event\player\PlayerEvent;
use pocketmine\Player;
use xxAROX\Emotes\Emote;


/**
 * Class PlayerEmoteEvent
 * @package xxAROX\Emotes\event
 * @author xxAROX
 * @date 25.04.2020 - 22:03
 * @project Emotes
 */
class PlayerEmoteEvent extends PlayerEvent implements Cancellable{
	/** @var Emote */
	protected $emote;


	/**
	 * PlayerEmoteEvent constructor.
	 * @param Player $player
	 * @param Emote $emote
	 */
	public function __construct(Player $player, Emote $emote){
		$this->player = $player;
		$this->emote = $emote;
	}

	/**
	 * Function getEmote
	 * @return Emote
	 */
	public function getEmote(): Emote{
		return $this->emote;
	}
}
