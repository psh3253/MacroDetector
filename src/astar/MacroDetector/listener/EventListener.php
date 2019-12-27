<?php

namespace MacroDetector\listener;

use MacroDetector\MacroDetector;
use MacroDetector\Queue;
use MacroDetector\task\TimeLimitTask;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\Listener;
use pocketmine\Player;

class EventListener implements Listener
{
    /**
     * @var MacroDetector
     */
    private $plugin;

    public function __construct(MacroDetector $plugin)
    {
        $this->plugin = $plugin;
        $plugin->getServer()->getPluginManager()->registerEvents($this, $plugin);
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool
    {
        if (!$sender->hasPermission("macrodetector.macro.reply"))
            return false;

        if ($command->getName() != "매크로")
            return false;

        if (!isset($args[0])) {
            if ($sender->isOp()) {
                $sender->sendMessage("§d[ §f매크로 §d] §f매크로 확인 <유저명> - 해당유저의 매크로 사용을 탐지합니다.");
                return true;
            } else {
                $sender->sendMessage("§d[ §f매크로 §d] §f매크로 <제시된 단어> - 채팅에 제시된 단어를 입력하세요.");
                return true;
            }
        }

        switch ($args[0]) {
            case "확인":
                if (!isset($args[1])) {
                    $sender->sendMessage("§d[ §f매크로 §d] §f매크로 확인 <유저명> - 해당유저의 매크로 사용을 탐지합니다.");
                    return true;
                } else {
                    $target = $this->plugin->getServer()->getPlayer($args[1]);
                    if ($target instanceof Player) {
                        $target->addTitle("§d매크로 테스트", "30초 이내에 채팅을 참고하여 매크로 테스트를 실시해주십시오.");
                        $target->sendMessage("§d[ §f매크로 §d] §f아래의 제시된 단어를 /매크로 <제시된 단어> 명령어로 채팅에 입력하세요.");
                        $rand = mt_rand(0, 9);
                        switch ($rand) {
                            case 0:
                                $word = "강아지";
                                break;
                            case 1:
                                $word = "고양이";
                                break;
                            case 2:
                                $word = "바나나";
                                break;
                            case 3:
                                $word = "지우개";
                                break;
                            case 4:
                                $word = "컴퓨터";
                                break;
                            case 5:
                                $word = "선풍기";
                                break;
                            case 6:
                                $word = "떡볶이";
                                break;
                            case 7 :
                                $word = "고등어";
                                break;
                            case 8:
                                $word = "마우스";
                                break;
                            case 9:
                                $word = "복숭아";
                                break;
                        }
                        $target->sendMessage("§d[ §f매크로 §d] §f단어 : §a" . $word . "");
                        $this->plugin->getScheduler()->scheduleDelayedTask($task = new TimeLimitTask($this->plugin, $target), 20 * 30);
                        $taskId = $task->getTaskId();
                        Queue::$checkQueue[$target->getName()] = array($word, $taskId);
                        return true;
                    } else {
                        $sender->sendMessage("§d[ §f매크로 §d] §f해당유저가 현재 접속중이 아닙니다.");
                        return true;
                    }
                }
                break;
            default:
                if (!isset(Queue::$checkQueue[$sender->getName()])) {
                    $sender->sendMessage("§d[ §f매크로 §d] §f매크로 테스트 대상이 아닙니다.");
                    return true;
                } else {
                    if ($args[0] == Queue::$checkQueue[$sender->getName()][0]) {
                        $this->plugin->getScheduler()->cancelTask(Queue::$checkQueue[$sender->getName()][1]);
                        return true;
                    }
                }
        }
    }
}