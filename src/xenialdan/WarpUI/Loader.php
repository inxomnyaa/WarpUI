<?php

namespace xenialdan\WarpUI;

use pocketmine\level\Location;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;
use pocketmine\utils\Config;


class Loader extends PluginBase
{
    /** @var Loader */
    private static $instance = null;
    /** @var Config */
    private $warps;

    /**
     * @param Location $location
     * @param string $name
     * @return bool
     */
    public static function addWarp(Location $location, string $name): bool
    {
        self::getInstance()->warps->set($name, ["x" => $location->getX(), "y" => $location->getY(), "z" => $location->getZ(), "levelname" => $location->getLevel()->getName(), "yaw" => $location->getYaw(), "pitch" => $location->getPitch()]);
        self::getInstance()->warps->save();
        return true;
    }

    /**
     * @param string $name
     * @return null|Location
     */
    public static function getWarp(string $name): ?Location
    {
        $values = self::getInstance()->warps->get($name);
        if ($values === false) return null;
        return new Location($values["x"], $values["y"], $values["z"], $values["yaw"], $values["pitch"], Server::getInstance()->getLevelByName($values["levelname"]));
    }

    /**
     * @return string[]
     */
    public static function getWarps()
    {
        return self::getInstance()->warps->getAll(true);
    }

    /**
     * @param $name
     * @return bool
     */
    public static function removeWarp($name)
    {
        if (self::getInstance()->warps->get($name) === false) return false;
        self::getInstance()->warps->remove($name);
        self::getInstance()->warps->save();
        return true;
    }

    public function onLoad()
    {
        self::$instance = $this;
        $this->warps = new Config($this->getDataFolder() . "warps.yml");
    }

    public function onEnable()
    {
        $this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
        $this->getServer()->getCommandMap()->register(Commands::class, new Commands($this));
    }

    /**
     * Returns an instance of the plugin
     * @return Loader
     */
    public static function getInstance()
    {
        return self::$instance;
    }
}