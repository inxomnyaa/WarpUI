<?php

namespace xenialdan\WarpUI\subcommand;

use InvalidArgumentException;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use xenialdan\customui\elements\Button;
use xenialdan\customui\windows\SimpleForm;
use xenialdan\WarpUI\Loader;

class RemoveSubCommand extends SubCommand
{

    /**
     * @param CommandSender $sender
     * @return bool
     */
    public function canUse(CommandSender $sender): bool
    {
        return ($sender instanceof Player) and $sender->hasPermission('warpui.command.warp.remove');
    }

    public function getUsage(): string
    {
        return 'remove';
    }

    public function getName(): string
    {
        return 'remove';
    }

    public function getDescription(): string
    {
        return 'Remove a warp';
    }

    public function getAliases(): array
    {
        return [];
    }

    /**
     * @param CommandSender $sender
     * @param array $args
     * @return bool
     * @throws InvalidArgumentException
     */
    public function execute(CommandSender $sender, array $args): bool
    {
        /** @var Player $sender */
        $form = new SimpleForm(TextFormat::DARK_RED . 'Remove warps', 'Click a warp to remove it');
        foreach (Loader::getWarps() as $warp) {
            $form->addButton(new Button($warp));
        }
        $form->setCallable(static function (Player $player, $data) {
            if (Loader::removeWarp($data)) {
                $player->sendMessage(TextFormat::GREEN . 'Removed ' . $data . TextFormat::RESET . TextFormat::GREEN . ' from the warp item');
            } else {
                $player->sendMessage(TextFormat::RED . 'Incorrect warp name');
            }
        }
        );
        $sender->sendForm($form);
        return true;
    }
}
