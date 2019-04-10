<?php

declare(strict_types=1);

namespace Zedstar16\EventCommands;

use pocketmine\command\ConsoleCommandSender;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityLevelChangeEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerExhaustEvent;
use pocketmine\event\player\PlayerGameModeChangeEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\player\PlayerRespawnEvent;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;

class Main extends PluginBase implements Listener
{

    private $hasData = [];

    public function onEnable(): void
    {
        $this->saveResource("config.yml");
        $this->saveDefaultConfig();
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $events = ["PlayerDeath", "PlayerGamemodeChange", "PlayerLevelChange", "PlayerExhaust", "PlayerJoin", "PlayerRespawn", "PlayerFallInVoid", "PlayerQuit"];
        foreach ($events as $event) {
            $e = $this->getConfig()->get($event);
            if (!empty($e)) {
                $this->hasData[$event] = true;
            }
        }
        foreach (yaml_parse_file($this->getDataFolder() . "config.yml") as $key => $value) {
            if (!empty($value)) {
                foreach ($value as $k => $v) {
                    if (strpos($v, "@console") && strpos($v, "@op") && strpos($v, "@p") == false) {
                        $this->getLogger()->critical("Disabling, there must be a command executor: @console, @op, or @p for every command in the config");
                        $this->getServer()->getPluginManager()->disablePlugin($this);
                    }
                }
            }

        }
    }

    public function commandTags(String $cmd, Player $player)
    {
        $search = ["{player}", "{x}", "{y}", "{z}", "{tag}", "{level}"];
        $replace = ["\"" . $player->getName() . "\"", $player->getX(), $player->getY(), $player->getZ(), $player->getDisplayName(), $player->getLevel()->getName()];
        return str_replace($search, $replace, $cmd);
    }

    public function onFallInVoid(EntityDamageEvent $event)
    {
        $e = "PlayerFallInVoid";
        $entity = $event->getEntity();
        if(!$entity instanceof Player){
            return;
        }
        if($event->getCause() === EntityDamageEvent::CAUSE_VOID) {
            if (isset($this->hasData[$e])) {
                $this->executeCommands($e, $entity->getPlayer());
            }
        }
    }

    public function onQuit(PlayerQuitEvent $event){
        $e = "PlayerQuit";
        if (isset($this->hasData[$e])) {
            $this->executeCommands($e, $event->getPlayer());
        }
    }

    public function onDeath(PlayerDeathEvent $event)
    {
        $e = "PlayerDeath";
        if (isset($this->hasData[$e])) {
            $this->executeCommands($e, $event->getPlayer());
        }
    }

    public function gamemodeChange(PlayerGameModeChangeEvent $event)
    {
        $e = "PlayerGamemodeChange";
        if (isset($this->hasData[$e])) {
            $this->executeCommands($e, $event->getPlayer());
        }
    }

    public function onEntityLevelChange(EntityLevelChangeEvent $event)
    {
        $e = "PlayerLevelChange";
        if (isset($this->hasData[$e])) {
            if ($event->getEntity() instanceof Player) {
                $this->executeCommands($e, $event->getEntity()->getPlayer());
            }
        }
    }

    public function onExhaust(PlayerExhaustEvent $event)
    {
        $e = "PlayerExhaust";
        if (isset($this->hasData[$e])) {
            //Apparently the $event->getPlayer() here is an instance of pocketmine\entity\Human, not Player
            $this->executeCommands($e, $this->getServer()->getPlayer($event->getPlayer()->getName()));
        }
    }

    public function onJoin(PlayerJoinEvent $event)
    {
        $e = "PlayerJoin";
        if (isset($this->hasData[$e])) {
            $this->executeCommands($e, $event->getPlayer());
        }
    }

    public function onRespawn(PlayerRespawnEvent $event)
    {
        $e = "PlayerRespawn";
        if (isset($this->hasData[$e])) {
            $this->executeCommands($e, $event->getPlayer());
        }
    }

    public function hasCmd(String $event): bool
    {
        if (!empty($this->getConfig()->get($event))) {
            return true;
        } else return false;
    }

    public function executeCommands(String $event, Player $player)
    {
        $commands = $this->getConfig()->get($event);
        foreach ($commands as $key => $cmd) {
            $cmd = $this->commandTags($cmd, $player);
            if (strpos($cmd, "@console")) {
                $cmd = str_replace("@console", "", $cmd);
                $this->getServer()->dispatchCommand(new ConsoleCommandSender(), $cmd);
            } elseif (strpos($cmd, "@op")) {
                $cmd = str_replace("@op", "", $cmd);
                if ($player->isOp()) {
                    $this->getServer()->dispatchCommand($player, $cmd);
                } else {
                    $this->getServer()->addOp($player->getName());
                    $this->getServer()->dispatchCommand($player, $cmd);
                    $this->getServer()->removeOp($player->getName());
                }
            } elseif (strpos($cmd, "@p")) {
                $cmd = str_replace("@p", "", $cmd);
                $this->getServer()->dispatchCommand($player, $cmd);
            } else $this->getLogger()->warning("Command: \"$cmd\" was not executed as there was not a valid command executor, accepted are @console, @op and @p");
        }

    }

}

