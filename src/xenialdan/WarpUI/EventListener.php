<?php

namespace xenialdan\WarpUI;

use pocketmine\event\entity\EntityLevelChangeEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

/**
 * Class EventListener
 * @package xenialdan\WarpUI
 */
class EventListener implements Listener
{

    /**
     * @param PlayerInteractEvent $event
     * @throws \BadMethodCallException
     * @throws \InvalidArgumentException
     */
    public function onInteract(PlayerInteractEvent $event)
    {
        $player = $event->getPlayer();
        #if (($level = ($player = $event->getPlayer())->getLevel())->getId() !== Loader::getInstance()->getServer()->getDefaultLevel()->getId()) return;
        if (($item = $event->getItem())->getId() === ItemIds::COMPASS && $item->hasCustomName() && $item->getCustomName() === TextFormat::DARK_PURPLE . "Warps") {
            $event->setCancelled();
            Loader::getInstance()->showWarpUI($player);
        }
        if (($item = $event->getItem())->getId() === ItemIds::CLOCK && $item->hasCustomName() && $item->getCustomName() === TextFormat::DARK_PURPLE . "Worlds") {
            $event->setCancelled();
            Loader::getInstance()->showWorldUI($player);
        }
    }

    /**
     * @param PlayerJoinEvent $event
     */
    public function onJoin(PlayerJoinEvent $event)
    {
        if (($level = ($player = $event->getPlayer())->getLevel())->getId() !== Loader::getInstance()->getServer()->getDefaultLevel()->getId()) return;
        $compass = ItemFactory::get(ItemIds::COMPASS)->setCustomName(TextFormat::DARK_PURPLE . "Warps");
        if (!$player->getInventory()->contains($compass))
            $player->getInventory()->addItem($compass);
        $clock = ItemFactory::get(ItemIds::CLOCK)->setCustomName(TextFormat::DARK_PURPLE . "Worlds");
        if (!$player->getInventory()->contains($clock))
            $player->getInventory()->addItem($clock);
    }

    /**
     * @param EntityLevelChangeEvent $event
     * @throws \BadMethodCallException
     */
    public function onLevelChange(EntityLevelChangeEvent $event)
    {
        /** @var Player $player */
        if (!($player = $event->getEntity()) instanceof Player || $event->isCancelled()) return;
        if (($level = $event->getTarget())->getId() !== Loader::getInstance()->getServer()->getDefaultLevel()->getId()) return;
        $compass = ItemFactory::get(ItemIds::COMPASS)->setCustomName(TextFormat::DARK_PURPLE . "Warps");
        if (!$player->getInventory()->contains($compass))
            $player->getInventory()->addItem($compass);
        $clock = ItemFactory::get(ItemIds::CLOCK)->setCustomName(TextFormat::DARK_PURPLE . "Worlds");
        if (!$player->getInventory()->contains($clock))
            $player->getInventory()->addItem($clock);
    }
}