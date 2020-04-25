<?php
/* Copyright (c) 2020 xxAROX. All rights reserved. */
namespace xxAROX\Emotes\command;
use Frago9876543210\EasyForms\elements\Button;
use Frago9876543210\EasyForms\forms\MenuForm;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\TextFormat;
use xxAROX\Emotes\Emote;
use xxAROX\Emotes\Main;
use xxAROX\LanguageAPI\LanguageAPI;


/**
 * Class EmoteCommand
 * @package xxAROX\Emotes\command
 * @author xxAROX
 * @date 25.04.2020 - 23:19
 * @project Emotes
 */
class EmoteCommand extends Command
{
	/**
	 * EmoteCommand constructor.
	 * @param string $name
	 * @param string $description
	 * @param string|NULL $usageMessage
	 * @param array $aliases
	 */
	public function __construct(string $name){
		parent::__construct($name, "Mirror yourself via an Emote.", "/emote", []);
		$this->setPermission("xxarox.command.emote");
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args){
		if (!$sender instanceof Player) {
			LanguageAPI::sendMessage($sender, "message.onlyPlayer");
			return;
		}
		if (!$this->testPermission($sender)) {
			return;
		}
		$buttons = [];
		foreach (Main::$emotes as $emote) {
			if (!is_null($emote->getPermission())) {
				$buttons[] = $sender->hasPermission($emote->getPermission()) ? new Button("§a" . $emote->getName()) : new Button("§c" . $emote->getName());
			} else {
				$buttons[] = new Button("§a" . $emote->getName());
			}
		}
		$sender->sendForm(new MenuForm(
			LanguageAPI::translate($sender, "ui.title.emotes"),
			"",
			$buttons,
			function (Player $player, Button $button): void{
				$emote = Main::getInstance()->getUtils()->getEmoteByName(TextFormat::clean($button->getText()));

				if (!$emote instanceof Emote) {
					LanguageAPI::sendMessage($player, "message.emoteNotFound", [TextFormat::clean($button->getText())]);
					return;
				}
				$emote->sendTo($player);
			},
			function (Player $player): void{}
		));
	}
}
