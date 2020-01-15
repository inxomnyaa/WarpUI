<?php

namespace xenialdan\WarpUI\subcommand;

use InvalidArgumentException;
use InvalidStateException;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\TextFormat;
use xenialdan\customui\elements\Button;
use xenialdan\customui\windows\SimpleForm;
use xenialdan\WarpUI\Loader;

class RemoveSubCommand extends SubCommand
{

    /**
     * @param CommandSender $sender
     * @return bool
     * @throws InvalidStateException
     */
    public function canUse(CommandSender $sender)
    {
        return ($sender instanceof Player) and $sender->hasPermission("warpui.command.remove");
    }

    public function getUsage()
    {
        return "remove";
    }

    public function getName()
    {
        return "remove";
    }

    public function getDescription()
    {
        return "Remove a warp";
    }

    public function getAliases()
    {
        return [];
    }

    /**
     * @param CommandSender $sender
     * @param array $args
     * @return bool
     * @throws InvalidArgumentException
     */
    public function execute(CommandSender $sender, array $args)
    {
        /** @var Player $sender */
        $form = new SimpleForm(TextFormat::DARK_RED . "Remove warps", "Click a warp to remove it");
        foreach (Loader::getWarps() as $warp) {
            $form->addButton(new Button($warp));
        }
        $form->setCallable(function (Player $player, $data) {
            if (Loader::removeWarp($data)) {
                $player->sendMessage(TextFormat::GREEN . 'Removed ' . $data . TextFormat::RESET . TextFormat::GREEN . ' from the warp item');
            } else
                $player->sendMessage(TextFormat::RED . 'Incorrect warp name');
        }
        );
        $sender->sendForm($form);
        return true;
    }
}
