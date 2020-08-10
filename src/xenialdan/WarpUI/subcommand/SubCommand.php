<?php

namespace xenialdan\WarpUI\subcommand;

use pocketmine\command\CommandSender;
use pocketmine\plugin\PluginOwned;
use pocketmine\plugin\PluginOwnedTrait;

abstract class SubCommand implements PluginOwned
{
    use PluginOwnedTrait;

    /**
     * @param CommandSender $sender
     * @return bool
     */
    abstract public function canUse(CommandSender $sender): bool;

    /**
     * @return string
     */
    abstract public function getUsage(): string;

    /**
     * @return string
     */
    abstract public function getName(): string;

    /**
     * @return string
     */
    abstract public function getDescription(): string;

    /**
     * @return string[]
     */
    abstract public function getAliases(): array;

    /**
     * @param CommandSender $sender
     * @param string[] $args
     * @return bool
     */
    abstract public function execute(CommandSender $sender, array $args): bool;
}
