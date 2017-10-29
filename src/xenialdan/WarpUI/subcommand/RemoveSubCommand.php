<?php

namespace xenialdan\WarpUI\subcommand;

use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\TextFormat;
use xenialdan\WarpUI\Loader;

class RemoveSubCommand extends SubCommand{

	public function canUse(CommandSender $sender){
		return ($sender instanceof Player) and $sender->hasPermission("warpUI.command.remove");
	}

	public function getUsage(){
		return "remove <name>";
	}

	public function getName(){
		return "remove";
	}

	public function getDescription(){
		return "Remove a warp";
	}

	public function getAliases(){
		return [];
	}

	/**
	 * @param CommandSender $sender
	 * @param array $args
	 * @return bool
	 */
	public function execute(CommandSender $sender, array $args){
		if (empty($args)) return false;
		$name = implode(' ', $args);
		if (Loader::removeWarp($name)){
			$sender->sendMessage(TextFormat::GREEN . 'Removed ' . $name . ' from the warp item');
			return true;
		}
		$sender->sendMessage(TextFormat::RED . 'Incorrect warp name');
		return false;
	}
}
