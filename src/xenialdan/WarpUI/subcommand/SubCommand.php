<?php

namespace xenialdan\WarpUI\subcommand;

use pocketmine\command\CommandSender;
use pocketmine\plugin\Plugin;

abstract class SubCommand
{
    /** @var Plugin */
    private $plugin;

    /**
     * @param Plugin $plugin
     */
    public function __construct(Plugin $plugin)
    {
        $this->plugin = $plugin;
    }

    /**
     * @return Plugin
     */
    final public function getPlugin(): Plugin
    {
        return $this->plugin;
    }

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
