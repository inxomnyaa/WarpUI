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
use xenialdan\WarpUI\subcommand\AddSubCommand;
use xenialdan\WarpUI\subcommand\ListSubCommand;
use xenialdan\WarpUI\subcommand\RemoveSubCommand;
use xenialdan\WarpUI\subcommand\SubCommand;
use xenialdan\WarpUI\subcommand\TeleportSubCommand;

class WarpUICommands extends Command implements PluginOwned
{
    use PluginOwnedTrait;

    private $subCommands = [];

    /* @var SubCommand[] */
    private $commandObjects = [];

    public function __construct(Plugin $owningPlugin)
    {
        $this->owningPlugin = $owningPlugin;
        parent::__construct('warpui');
        $this->setPermission('warpui.command.warp');
        $this->setDescription('Manages warps of WarpUI');

        $this->loadSubCommand(new AddSubCommand($owningPlugin));
        $this->loadSubCommand(new ListSubCommand($owningPlugin));
        $this->loadSubCommand(new RemoveSubCommand($owningPlugin));
        $this->loadSubCommand(new TeleportSubCommand($owningPlugin));
    }

    private function loadSubCommand(SubCommand $command): void
    {
        $this->commandObjects[] = $command;
        $commandId = count($this->commandObjects) - 1;
        $this->subCommands[$command->getName()] = $commandId;
        foreach ($command->getAliases() as $alias) {
            $this->subCommands[$alias] = $commandId;
        }
    }

    /**
     * @param CommandSender $sender
     * @param string $commandLabel
     * @param array $args
     * @return bool|mixed
     * @throws InvalidArgumentException
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if (!isset($args[0])) {
            if (!$sender instanceof Player) {
                $sender->sendMessage(TextFormat::RED . 'This command must be run ingame');
                return true;
            }
            if (!$sender->hasPermission($this->getPermission())) {
                $sender->sendMessage(TextFormat::RED . 'No permission');
                return true;
            }
            Loader::getInstance()->showWarpUI($sender);
            return true;
        }
        $subCommand = strtolower(array_shift($args));
        if (!isset($this->subCommands[$subCommand])) {
            return $this->sendHelp($sender);
        }
        $command = $this->commandObjects[$this->subCommands[$subCommand]];
        $canUse = $command->canUse($sender);
        if ($canUse) {
            if (!$command->execute($sender, $args)) {
                $sender->sendMessage(TextFormat::YELLOW . 'Usage: /warpui ' . $command->getName() . TextFormat::BOLD . TextFormat::DARK_AQUA . ' > ' . TextFormat::RESET . TextFormat::YELLOW . $command->getUsage());
            }
        } else if (!($sender instanceof Player)) {
            $sender->sendMessage(TextFormat::RED . 'Please run this command in-game.');
        } else {
            $sender->sendMessage(TextFormat::RED . 'You do not have permissions to run this command');
        }
        return true;
    }

    private function sendHelp(CommandSender $sender): bool
    {
        $sender->sendMessage('===========[WarpUI commands]===========');
        foreach ($this->commandObjects as $command) {
            if ($command->canUse($sender)) {
                $sender->sendMessage(TextFormat::DARK_GREEN . '/warpui ' . $command->getName() . TextFormat::BOLD . TextFormat::DARK_AQUA . ' > ' . TextFormat::RESET . TextFormat::DARK_GREEN . $command->getUsage() . ': ' .
                    TextFormat::WHITE . $command->getDescription()
                );
            }
        }
        return true;
    }
}
