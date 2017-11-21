<?php

namespace xenialdan\WarpUI\subcommand;

use pocketmine\command\CommandSender;
use pocketmine\form\Form;
use pocketmine\form\MenuForm;
use pocketmine\form\MenuOption;
use pocketmine\Player;
use pocketmine\utils\TextFormat;
use xenialdan\WarpUI\Loader;

class RemoveSubCommand extends SubCommand{

	public function canUse(CommandSender $sender){
		return ($sender instanceof Player) and $sender->hasPermission("warpui.command.remove");
	}

	public function getUsage(){
		return "remove";
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
		/** @var Player $sender */
		$warps = array_map(function ($name){
			return new MenuOption($name);
		}, Loader::getWarps());
		$sender->sendForm(
			new class(TextFormat::DARK_RED . "Remove warps", "Click a warp to remove it", $warps) extends MenuForm{
				public function onSubmit(Player $player): ?Form{
					$selectedOption = $this->getSelectedOption()->getText();
					if (Loader::removeWarp($selectedOption)){
						$player->sendMessage(TextFormat::GREEN . 'Removed ' . $selectedOption . TextFormat::RESET . TextFormat::GREEN . ' from the warp item');
					} else
						$player->sendMessage(TextFormat::RED . 'Incorrect warp name');
					return null;
				}
			}
			, true
		);
		return true;
	}
}
