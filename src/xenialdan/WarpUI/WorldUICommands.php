<?php

namespace xenialdan\WarpUI;

use InvalidArgumentException;
use InvalidStateException;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginCommand;
use pocketmine\Player;
use pocketmine\plugin\Plugin;
use pocketmine\utils\TextFormat;

class WorldUICommands extends PluginCommand
{

    public function __construct(Plugin $plugin)
    {
        parent::__construct('worldui', $plugin);
        $this->setPermission('warpui.command.world');
        $this->setDescription('Shows the world teleport UI');
    }

    /**
     * @param CommandSender $sender
     * @param string $commandLabel
     * @param array $args
     * @return bool
     * @throws InvalidArgumentException
     * @throws InvalidStateException
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args): bool
    {
        if (!$sender instanceof Player) {
            $sender->sendMessage(TextFormat::RED . 'This command must be run ingame');
            return true;
        }
        if (!$sender->hasPermission($this->getPermission())) {
            $sender->sendMessage(TextFormat::RED . 'No permission');
            return true;
        }
        Loader::getInstance()->showWorldUI($sender);
        return true;
    }
}
