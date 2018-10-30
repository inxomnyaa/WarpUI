<?php

namespace xenialdan\WarpUI;

use pocketmine\event\entity\EntityLevelChangeEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\Player;
use pocketmine\plugin\Plugin;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use xenialdan\customui\elements\Button;
use xenialdan\customui\windows\SimpleForm;

class EventListener implements Listener
{
    /** @var Loader */
    public $owner;

    public function __construct(Plugin $plugin)
    {
        $this->owner = $plugin;
    }

    public function onInteract(PlayerInteractEvent $event)
    {
        if (($level = ($player = $event->getPlayer())->getLevel())->getId() !== Server::getInstance()->getDefaultLevel()->getId()) return;
        if (($item = $event->getItem())->getId() !== ItemIds::COMPASS) return;
        $event->setCancelled();
        $form = new SimpleForm(TextFormat::DARK_PURPLE . "Warps", "Teleport to any warp");
        foreach (Loader::getWarps() as $warp) {
            $form->addButton(new Button($warp));
        }
        $form->setCallable(function (Player $player, $data) {
            $location = Loader::getWarp($data);
            if (!is_null($location)) $player->teleport($location);
            else $player->sendMessage("Warp was not found. Please contact an administrator about this\nError: Warp not found\nWarpname: " . $data);
        }
        );
        $player->sendForm($form);
    }

    public function onJoin(PlayerJoinEvent $event)
    {
        if (($level = ($player = $event->getPlayer())->getLevel())->getId() !== Server::getInstance()->getDefaultLevel()->getId()) return;
        $compass = ItemFactory::get(ItemIds::COMPASS)->setCustomName(TextFormat::DARK_PURPLE . "Warps");
        if (!$player->getInventory()->contains($compass))
            $player->getInventory()->addItem($compass);
    }

    public function onLevelChange(EntityLevelChangeEvent $event)
    {
        /** @var Player $player */
        if (!($player = $event->getEntity()) instanceof Player) return;
        if (($level = $event->getTarget())->getId() !== Server::getInstance()->getDefaultLevel()->getId()) return;
        $compass = ItemFactory::get(ItemIds::COMPASS)->setCustomName(TextFormat::DARK_PURPLE . "Warps");
        if (!$player->getInventory()->contains($compass))
            $player->getInventory()->addItem($compass);
    }
}