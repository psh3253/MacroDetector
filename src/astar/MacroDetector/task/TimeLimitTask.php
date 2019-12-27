<?php

namespace astar\MacroDetector\task;

use astar\MacroDetector\MacroDetector;
use pocketmine\Player;
use pocketmine\scheduler\Task;

class TimeLimitTask extends Task
{
    /**
     * @var MacroDetector
     */
    private $owner;

    /**
     * @var Player
     */
    private $player;

    public function __construct(MacroDetector $owner, Player $player)
    {
        $this->owner = $owner;
        $this->player = $player;
    }

    public function onRun(int $currentTick)
    {
        $this->owner->TimeOut($this->player);
    }

    public function onCancel()
    {
        $this->owner->TestSuccess($this->player);
    }
}