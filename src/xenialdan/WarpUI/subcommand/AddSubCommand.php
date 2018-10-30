<?php

namespace xenialdan\WarpUI\subcommand;

use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\TextFormat;
use xenialdan\WarpUI\Loader;

class AddSubCommand extends SubCommand
{

    public function canUse(CommandSender $sender)
    {
        return ($sender instanceof Player) and $sender->hasPermission("warpui.command.add");
    }

    public function getUsage()
    {
        return "add <name>";
    }

    public function getName()
    {
        return "add";
    }

    public function getDescription()
    {
        return "Add a warp point";
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
        $name = implode(' ', $args);
        /** @var Player $sender */
        $location = $sender->getLocation();
        if (Loader::addWarp($location, $name)) {
            $sender->sendMessage(TextFormat::GREEN . 'Added ' . $name . ' at ' . $location . ' to the warp item');
            return true;
        } else return false;
    }
}
