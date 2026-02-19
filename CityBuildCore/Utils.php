<?php

namespace CityBuildCore;

use pocketmine\player\Player;

class ClearLagg {
    public function __construct(private Main $plugin){}
    public function start(): void {}
}

class ScoreboardManager {
    public function __construct(private Main $plugin){}
    public function start(): void {}
}
