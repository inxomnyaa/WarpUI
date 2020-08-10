<?php

namespace xenialdan\WarpUI;

use InvalidArgumentException;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\plugin\Plugin;
use pocketmine\plugin\PluginOwned;
use pocketmine\plugin\PluginOwnedTrait;
use pocketmine\utils\TextFormat;

class WorldUICommands extends Command implements PluginOwned
{
    use PluginOwnedTrait;

    public function __construct(Plugin $owningPlugin)
    {
        $this->owningPlugin = $owningPlugin;
        parent::__construct('worldui');
        $this->setPermission('warpui.command.world');
        $this->setDescription('Shows the world teleport UI');
    }

    /**
     * @param CommandSender $sender
     * @param string $commandLabel
     * @param array $args
     * @return bool
     * @throws InvalidArgumentException
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
        //TODO cleanup, very sloppy. I just need something that works, urgent
        if (count($args) >= 1) {
            if (is_string(($world = $args[0]))) {
                if ($sender->hasPermission("warpui.world.$world")) {
                    if ($this->owningPlugin->getServer()->getWorldManager()->loadWorld($world)) {
                        $sender->teleport($this->owningPlugin->getServer()->getWorldManager()->getWorldByName($world)->getSpawnLocation());
                    } else {
                        $sender->sendMessage(TextFormat::RED . 'World not found');
                    }
                } else {
                    $sender->sendMessage(TextFormat::RED . 'No permission');
                }
            }
            return true;
        }
        Loader::getInstance()->showWorldUI($sender);
        return true;
    }
}
