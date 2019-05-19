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
use xenialdan\gameapi\API;

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
        $player = $event->getPlayer();
        #if (($level = ($player = $event->getPlayer())->getLevel())->getId() !== Server::getInstance()->getDefaultLevel()->getId()) return;
        if (($item = $event->getItem())->getId() === ItemIds::COMPASS) {
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
        if (($item = $event->getItem())->getId() === ItemIds::CLOCK) {
            $event->setCancelled();
            $form = new SimpleForm(TextFormat::DARK_PURPLE . "Worlds", "Teleport to any world");
            foreach (API::getAllWorlds() as $warp) {
                $form->addButton(new Button($warp));
            }
            $form->setCallable(function (Player $player, $data) {
                Loader::getInstance()->getServer()->loadLevel($data);
                $location = Loader::getInstance()->getServer()->getLevelByName($data)->getSpawnLocation();
                if (!is_null($location)) $player->teleport($location);
                else $player->sendMessage("World was not found. Please contact an administrator about this\nError: Something is wrong with the world location\nWorld: " . $data);
            }
            );
            $player->sendForm($form);
        }
    }

    public function onJoin(PlayerJoinEvent $event)
    {
        if (($level = ($player = $event->getPlayer())->getLevel())->getId() !== Server::getInstance()->getDefaultLevel()->getId()) return;
        $compass = ItemFactory::get(ItemIds::COMPASS)->setCustomName(TextFormat::DARK_PURPLE . "Warps");
        if (!$player->getInventory()->contains($compass))
            $player->getInventory()->addItem($compass);
        $clock = ItemFactory::get(ItemIds::CLOCK)->setCustomName(TextFormat::DARK_PURPLE . "Worlds");
        if (!$player->getInventory()->contains($clock))
            $player->getInventory()->addItem($clock);
    }

    public function onLevelChange(EntityLevelChangeEvent $event)
    {
        /** @var Player $player */
        if (!($player = $event->getEntity()) instanceof Player || $event->isCancelled()) return;
        if (($level = $event->getTarget())->getId() !== Server::getInstance()->getDefaultLevel()->getId()) return;
        $compass = ItemFactory::get(ItemIds::COMPASS)->setCustomName(TextFormat::DARK_PURPLE . "Warps");
        if (!$player->getInventory()->contains($compass))
            $player->getInventory()->addItem($compass);
        $clock = ItemFactory::get(ItemIds::CLOCK)->setCustomName(TextFormat::DARK_PURPLE . "Worlds");
        if (!$player->getInventory()->contains($clock))
            $player->getInventory()->addItem($clock);
    }
}