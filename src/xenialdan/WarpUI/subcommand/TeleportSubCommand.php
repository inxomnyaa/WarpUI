<?php

namespace xenialdan\WarpUI\subcommand;

use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use pocketmine\world\WorldException;
use xenialdan\WarpUI\Loader;

class TeleportSubCommand extends SubCommand
{
    /**
     * @param CommandSender $sender
     * @return bool
     */
    public function canUse(CommandSender $sender): bool
    {
        return ($sender instanceof Player) and $sender->hasPermission('warpui.command.warp.teleport');
    }

    public function getUsage(): string
    {
        return 'teleport <warp name>';
    }

    public function getName(): string
    {
        return 'teleport';
    }

    public function getDescription(): string
    {
        return 'Teleport to a warp via name';
    }

    public function getAliases(): array
    {
        return [];
    }

    /**
     * @param CommandSender $sender
     * @param array $args
     * @return bool
     * @throws WorldException
     */
    public function execute(CommandSender $sender, array $args): bool
    {
        if (empty($args)) {
            return false;
        }
        /** @var Player $sender */
        $warpname = implode(' ', $args);
        if ($sender->hasPermission('warpui.world') || $sender->hasPermission('warpui.world.*') || $sender->hasPermission('warpui.world.' . TextFormat::clean(strtolower($warpname)))) {
            $location = Loader::getWarp($warpname);
            if ($location !== null) {
                if (!$location->isValid()) {
                    $sender->sendMessage(TextFormat::RED . 'Target level is not loaded yet. Try again.');
                    return true;
                }
                if ($sender->teleport($location)) {
                    $sender->sendMessage(TextFormat::GREEN . 'Teleported to ' . $warpname);
                }
            }
        } else {
            $sender->sendMessage(TextFormat::RED . "You do not have permission to go to the warp $warpname");
        }
        return true;
    }
}
