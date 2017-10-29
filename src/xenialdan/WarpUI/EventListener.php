<?php

namespace xenialdan\WarpUI;

use pocketmine\event\entity\EntityLevelChangeEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\form\Form;
use pocketmine\form\MenuForm;
use pocketmine\form\MenuOption;
use pocketmine\form\ModalForm;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\Player;
use pocketmine\plugin\Plugin;
use pocketmine\Server;
use pocketmine\utils\TextFormat;

class EventListener implements Listener{
	/** @var Loader */
	public $owner;

	public function __construct(Plugin $plugin){
		$this->owner = $plugin;
	}

	public function onInteract(PlayerInteractEvent $event){
		if (($level = ($player = $event->getPlayer())->getLevel())->getId() !== Server::getInstance()->getDefaultLevel()->getId()) return;
		if (($item = $event->getItem())->getId() !== ItemIds::COMPASS) return;
		$event->setCancelled();
		$warps = array_map(function ($name){
			return new MenuOption($name);
		}, Loader::getWarps());
		$player->sendForm(
			new class(TextFormat::DARK_PURPLE . "Warps", "Teleport to any warp", ...$warps) extends MenuForm{
				public function onSubmit(Player $player): ?Form{
					$selectedOption = $this->getSelectedOption()->getText();
					$location = Loader::getWarp($selectedOption);
					if (!is_null($location)) $player->teleport($location);
					else $player->sendForm(new class("Warp was not found", "Please contact an administrator about this\nError: Warp not found\nWarpname: " . $selectedOption, "gui.yes", "gui.no") extends ModalForm{
					}, true);
					return null;
				}
			}
			, true
		);
	}

	public function onJoin(PlayerJoinEvent $event){
		if (($level = ($player = $event->getPlayer())->getLevel())->getId() !== Server::getInstance()->getDefaultLevel()->getId()) return;
		$compass = ItemFactory::get(ItemIds::COMPASS)->setCustomName(TextFormat::DARK_PURPLE . "Warps");
		if (!$player->getInventory()->contains($compass))
			$player->getInventory()->addItem($compass);
	}

	public function onLevelChange(EntityLevelChangeEvent $event){
		/** @var Player $player */
		if(!($player = $event->getEntity()) instanceof Player) return;
		if (($level = $event->getTarget())->getId() !== Server::getInstance()->getDefaultLevel()->getId()) return;
		$compass = ItemFactory::get(ItemIds::COMPASS)->setCustomName(TextFormat::DARK_PURPLE . "Warps");
		if (!$player->getInventory()->contains($compass))
			$player->getInventory()->addItem($compass);
	}
}