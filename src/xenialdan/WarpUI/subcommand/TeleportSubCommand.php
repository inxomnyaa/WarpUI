<?php

namespace xenialdan\WarpUI\subcommand;

use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\TextFormat;
use xenialdan\WarpUI\Loader;

class TeleportSubCommand extends SubCommand
{
    /**
     * @param CommandSender $sender
     * @return bool
     * @throws \InvalidStateException
     */
    public function canUse(CommandSender $sender)
    {
        return ($sender instanceof Player) and $sender->hasPermission("warpui.command.teleport");
    }

    public function getUsage()
    {
        return "teleport <warp name>";
    }

    public function getName()
    {
        return "teleport";
    }

    public function getDescription()
    {
        return "Teleport to a warp via name";
    }

    public function getAliases()
    {
        return [];
    }

    /**
     * @param CommandSender $sender
     * @param array $args
     * @return bool
     */
    public function execute(CommandSender $sender, array $args)
    {
        if (empty($args)) return false;
        /** @var Player $sender */
        $location = Loader::getWarp(implode(" ", $args));
        if (!is_null($location)) {
            if ($sender->teleport($location)) {
                $sender->sendMessage(TextFormat::GREEN . "Teleported to " . implode(" ", $args));
            }
        }
        return true;
    }
}
