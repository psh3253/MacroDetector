<?php

namespace MacroDetector;

use MacroDetector\listener\EventListener;
use pocketmine\command\PluginCommand;
use pocketmine\event\Listener;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;

class MacroDetector extends PluginBase implements Listener
{
    /**
     * @var EventListener
     */
    private $eventListener;

    private $data;

    public function onEnable()
    {
        $this->eventListener = new EventListener ($this);
        $this->registerCommand("매크로", "macrodetector.macro.answer", "매크로의 사용여부를 탐지합니다.", "매크로");
        $this->data = file_exists($this->getDataFolder()."log.yml") ? yaml_parse("log.yml") : [];
    }

    public function onDisable()
    {
        file_put_contents($this->getDataFolder()."log.yml", $this->data);
    }

    public function registerCommand($name, $permission, $description = "", $usage = "")
    {
        $commandMap = $this->getServer()->getCommandMap();
        $command = new PluginCommand($name, $this);
        $command->setPermission($permission);
        $command->setDescription($description);
        $command->setUsage($usage);
        $commandMap->register($name, $command);
    }

    public function TimeOut(Player $player)
    {
        $player->teleport(Server::getInstance()->getDefaultLevel()->getSafeSpawn());
        $player->sendMessage("§d[ §f매크로 §d] §f매크로 테스트 결과 매크로로 §c적발§f되었습니다.");
        $player->sendMessage("§d[ §f매크로 §d] §f적발이 누적될경우 운영자의 §c제재§f를 받을 수 있습니다.");
        $this->data[$player->getName()]["log"][] = date("Y-m-d H:i:s");
        $this->data[$player->getName()]["count"] += 1;
    }
    public function TestSuccess(Player $player)
    {
        $player->sendMessage("§d[ §f매크로 §d] §f매크로 테스트에 정상적으로 §a통과§f하였습니다.");
        foreach ($this->getServer()->getOps() as $player)
        {
            if($player instanceof Player)
            {
                $player->sendMessage("§d[ §f매크로 §d] §a".$player->getName()."님이 매크로 테스트에 통과혔습니다.");
            }
        }
    }
}
