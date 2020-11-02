<?php

namespace xenialdan\WarpUI;

use InvalidArgumentException;
use InvalidStateException;
use pocketmine\entity\Location;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\plugin\PluginException;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;
use pocketmine\world\WorldException;
use xenialdan\customui\elements\Button;
use xenialdan\customui\windows\SimpleForm;

class Loader extends PluginBase
{
    /** @var Loader */
    private static $instance;
    /** @var Config */
    private $warps;

    public function onLoad(): void
    {
        self::$instance = $this;
        $this->saveDefaultConfig();
        $this->warps = new Config($this->getDataFolder() . 'warps.yml');
    }

    /**
     * @throws PluginException
     */
    public function onEnable(): void
    {
        $this->getServer()->getPluginManager()->registerEvents(new EventListener(), $this);
        $this->getServer()->getCommandMap()->registerAll('WarpUI', [
                new WarpUICommands($this),
                new WorldUICommands($this)]
        );
    }

    /**
     * Returns an instance of the plugin
     * @return Loader
     */
    public static function getInstance(): Loader
    {
        return self::$instance;
    }

    /**
     * @param Location $location
     * @param string $name
     * @return bool
     * @throws InvalidStateException
     */
    public static function addWarp(Location $location, string $name): bool
    {
        self::getInstance()->warps->set($name, ['x' => $location->getX(), 'y' => $location->getY(), 'z' => $location->getZ(), 'levelname' => $location->getWorld()->getFolderName(), 'yaw' => $location->getYaw(), 'pitch' => $location->getPitch()]);
        self::getInstance()->warps->save();
        return true;
    }

    /**
     * @param string $name
     * @return null|Location
     * @throws WorldException
     */
    public static function getWarp(string $name): ?Location
    {
        $values = self::getInstance()->warps->get($name);
        if ($values === false) {
            return null;
        }
        self::getInstance()->getServer()->getWorldManager()->loadWorld($values['levelname']);
        return new Location($values['x'], $values['y'], $values['z'], $values['yaw'], $values['pitch'], self::getInstance()->getServer()->getWorldManager()->getWorldByName($values['levelname']));
    }

    /**
     * @return string[]
     */
    public static function getWarps(): array
    {
        return self::getInstance()->warps->getAll(true);
    }

    /**
     * @param $name
     * @return bool
     * @throws InvalidStateException
     */
    public static function removeWarp($name): bool
    {
        if (self::getInstance()->warps->get($name) === false) {
            return false;
        }
        self::getInstance()->warps->remove($name);
        self::getInstance()->warps->save();
        return true;
    }

    /**
     * @param Player $player
     * @throws InvalidArgumentException
     */
    public function showWarpUI(Player $player): void
    {
        $form = new SimpleForm(TextFormat::DARK_PURPLE . 'Warps', 'Click to teleport to a warp');
        foreach (self::getWarps() as $warp) {
            if ($player->hasPermission('warpui.warp') || $player->hasPermission('warpui.warp.*') || $player->hasPermission('warpui.warp.' . TextFormat::clean(strtolower($warp)))) {
                $form->addButton(new Button($warp));
            }
        }
        $form->setCallable(static function (Player $player, $data) {
            if ($player->hasPermission('warpui.warp') || $player->hasPermission('warpui.warp.*') || $player->hasPermission('warpui.warp.' . TextFormat::clean(strtolower($data)))) {
                $location = Loader::getWarp($data);
                if ($location !== null) {
                    if (!$location->isValid()) {
                        $player->sendMessage(TextFormat::RED . 'Target level is not loaded yet. Try again.');
                        return;
                    }
                    $player->teleport($location);
                } else {
                    $player->sendMessage("Warp was not found. Please contact an administrator about this\nError: Warp not found\nWarpname: " . $data);
                }
            } else {
                $player->sendMessage(TextFormat::RED . "You do not have permission to go to the warp $data");
            }
        }
        );
        $player->sendForm($form);
    }

    /**
     * @param Player $player
     * @throws InvalidArgumentException
     */
    public function showWorldUI(Player $player): void
    {
        $form = new SimpleForm(TextFormat::DARK_PURPLE . 'Worlds', 'Click to teleport to a world');
        foreach (self::getAllWorlds() as $world) {
            if ($player->hasPermission('warpui.world') || $player->hasPermission('warpui.world.*') || $player->hasPermission('warpui.world.' . TextFormat::clean(strtolower($world)))) {
                $form->addButton(new Button($world));
            }
        }
        $form->setCallable(static function (Player $player, $data) {
            if ($player->hasPermission('warpui.world') || $player->hasPermission('warpui.world.*') || $player->hasPermission('warpui.world.' . TextFormat::clean(strtolower($data)))) {
                Loader::getInstance()->getServer()->getWorldManager()->loadWorld($data);
                $level = Loader::getInstance()->getServer()->getWorldManager()->getWorldByName($data);
                if ($level !== null) {
                    $location = $level->getSpawnLocation();
                    $player->teleport($location);
                } else {
                    $player->sendMessage("World was not found. Please contact an administrator about this\nError: No world with the name " . $data . ' was found. Make sure to use the folder name');
                }
            } else {
                $player->sendMessage(TextFormat::RED . "You do not have permission to go to the world $data");
            }
        }
        );
        $player->sendForm($form);
    }

    /**
     * Returns all world folder names of valid levels in "/worlds"
     * @return string[]
     */
    private static function getAllWorlds(): array
    {
        $worldNames = [];
        $glob = glob(self::getInstance()->getServer()->getDataPath() . 'worlds/*', GLOB_ONLYDIR);
        if ($glob === false) {
            return $worldNames;
        }
        foreach ($glob as $path) {
			$worldNames[] = basename($path);
		}
        sort($worldNames);
        return $worldNames;
    }
}