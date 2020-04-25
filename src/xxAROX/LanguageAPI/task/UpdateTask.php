<?php
/* Copyright (c) 2020 xxAROX. All rights reserved. */
namespace xxAROX\LanguageAPI\task;
use pocketmine\scheduler\Task;
use xxAROX\LanguageAPI\LanguageAPI;


/**
 * Class UpdateTask
 * @package xxAROX\LanguageAPI\task
 * @author xxAROX
 * @date 02.04.2020 - 23:21
 * @project TrollSystem
 */
class UpdateTask extends Task
{
	/**
	 * Function onRun
	 * @param int $currentTick
	 * @return void
	 */
	public function onRun(int $currentTick){
		if (LanguageAPI::getInstance()->needsReload()) {
			LanguageAPI::getInstance()->reload();
		}
	}
}
