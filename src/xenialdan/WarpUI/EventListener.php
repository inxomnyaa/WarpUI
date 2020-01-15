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
    public function getGiveWarpItem():bool{
        return Loader::getInstance()->getConfig()->get("give-warp-item",true);
    }
    public function getGiveWorldItem():bool{
        return Loader::getInstance()->getConfig()->get("give-world-item",true);
    }
    public function getWarpItemName():string{
        return Loader::getInstance()->getConfig()->get("warp-item-name","§dWarps");
    }
    public function getWorldItemName():string{
        return Loader::getInstance()->getConfig()->get("world-item-name","§dWorlds");
    }
    public function getWarpItemType():string{
        return Loader::getInstance()->getConfig()->get("warp-item","compass");
    }
    public function getWorldItemType():string{
        return Loader::getInstance()->getConfig()->get("world-item","clock");
    }
    public function getWarpItem():Item{
        Item::fromString($this->getWarpItemType())->setCustomName($this->getWarpItemName());
    }
    public function getWorldItem():Item{
        Item::fromString($this->getWorldItemType())->setCustomName($this->getWorldItemName());
    }
    public function getWorlds():array{
        return Loader::getInstance()->getConfig()->get("worlds",[]);
    }

    /**
     * @param PlayerInteractEvent $event
     * @throws \BadMethodCallException
     * @throws \InvalidArgumentException
     */
    public function onInteract(PlayerInteractEvent $event):void
    {
        $player = $event->getPlayer();
        $level = $player->getLevel();
        $item = $event->getItem();
        if(empty($this->getWorlds()) || in_array($level->getName(), $this->getWorlds())){
            if ($item->equals($this->getWarpItem())) {
                $event->setCancelled();
                Loader::getInstance()->showWarpUI($player);
            }
            if ($item->equals($this->getWorldItem())) {
                $event->setCancelled();
                Loader::getInstance()->showWorldUI($player);
            }
        }
    }

    /**
     * @param PlayerJoinEvent $event
     */
    public function onJoin(PlayerJoinEvent $event):void
    {
        $player = $event->getPlayer();
        $level = $player->getLevel();
        $player->getInventory()->remove($this->getWarpItem());
        $player->getInventory()->remove($this->getWorldItem());
        if(empty($this->getWorlds()) || in_array($level->getName(), $this->getWorlds())){
            if($this->getGiveWarpItem())
                $player->getInventory()->addItem($this->getWarpItem());
            if($this->getGiveWorldItem())
                $player->getInventory()->addItem($this->getWorldItem());
        }
    }

    /**
     * @param EntityLevelChangeEvent $event
     * @throws \BadMethodCallException
     */
    public function onLevelChange(EntityLevelChangeEvent $event):void
    {
        $player = $event->getPlayer();
        $level = $event->getTarget();
        $player->getInventory()->remove($this->getWarpItem());
        $player->getInventory()->remove($this->getWorldItem());
        if(empty($this->getWorlds()) || in_array($level->getName(), $this->getWorlds())){
            if($this->getGiveWarpItem())
                $player->getInventory()->addItem($this->getWarpItem());
            if($this->getGiveWorldItem())
                $player->getInventory()->addItem($this->getWorldItem());
        }
    }
}