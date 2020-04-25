<?php
/* Copyright (c) 2020 xxAROX. All rights reserved. */
namespace xxAROX\LanguageAPI\command;
use Frago9876543210\EasyForms\elements\Button;
use Frago9876543210\EasyForms\forms\MenuForm;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use xxAROX\LanguageAPI\LanguageAPI;


/**
 * Class LanguageCommand
 * @package xxAROX\LanguageAPI\command
 * @author xxAROX
 * @date 28.03.2020 - 20:54
 * @project GetDown
 */
class LanguageCommand extends Command
{
	protected $languageAPI = NULL;


	/**
	 * LanguageCommand constructor.
	 * @param string $name
	 * @param LanguageAPI $languageAPI
	 */
	public function __construct(string $name, LanguageAPI $languageAPI){
		$this->languageAPI = $languageAPI;
		parent::__construct($name, "Select a language.", "/language <" . strtolower(join("|", $languageAPI->getGlobalProperties()->getNested("Language.languages", ["en_US"]))) . ">", ["lang"]);
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args){
		if (!$sender instanceof Player) {
			if (!isset($args[0])) {
				$sender->sendMessage("§7/language reload");
			} else {
				if (strtolower($args[0]) == "reload") {
					$this->languageAPI->reload(TRUE);
					$sender->sendMessage("§2LanguageAPI was successful reloaded.");
					$this->languageAPI->sendLoadedLanguages($sender);
				} else {
					$sender->sendMessage("§7/language reload");
				}
			}
			return;
		} else {
			if (!isset($args[0])) {
				$this->ui($sender);
				return;
			}
			if (strtolower($args[0]) == "reload") {
				if ($sender->hasPermission("xxarox.command.language.reload")) {
					LanguageAPI::sendMessage($sender, "languageApi.reloaded");
					$this->languageAPI->reload();
					$this->languageAPI->sendLoadedLanguages($sender);
				} else {
					$sender->sendMessage($this->getUsage());
				}
				return;
			}
		}
	}

	public function ui(Player $sender) {
		$vals = [];
		$buttons = [];

		foreach (LanguageAPI::getInstance()->getLanguages() as $language) {
			$buttons[] = new Button("{$language->getName()}");
			$vals[] = $language->getLocale();
		}
		$sender->sendForm(new MenuForm(
			LanguageAPI::translate($sender, "ui.title.selectLanguage"),
			LanguageAPI::translate($sender, "ui.text.selectLanguage"),
			$buttons,
			function (Player $player, Button $button) use ($vals): void{
				$selected = $vals[$button->getValue()];
				LanguageAPI::updateLanguage($player->getName(), $selected);
				LanguageAPI::sendMessage($player, "languageApi.wasUpdated", [$button->getText()]);
			}
		));
	}
}
