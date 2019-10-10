<?php

namespace xenialdan\WarpUI\subcommand;

use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\TextFormat;
use xenialdan\WarpUI\Loader;

class ListSubCommand extends SubCommand
{
    /**
     * @param CommandSender $sender
     * @return bool
     * @throws \InvalidStateException
     */
    public function canUse(CommandSender $sender)
    {
        return ($sender instanceof Player) and $sender->hasPermission("warpui.command.list");
    }

    public function getUsage()
    {
        return "list";
    }

    public function getName()
    {
        return "list";
    }

    public function getDescription()
    {
        return "Listing all warp names";
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
        $sender->sendMessage(TextFormat::AQUA . "Warpnames:\n" . implode("\n" . TextFormat::GOLD, Loader::getWarps()));
        return true;
    }
}
