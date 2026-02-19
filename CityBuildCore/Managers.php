<?php

namespace CityBuildCore;

use pocketmine\player\Player;
use pocketmine\world\World;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\block\VanillaBlocks;
use pocketmine\math\Vector3;

//
// ------------------ PlotManager ------------------
//

class PlotManager implements Listener {

    private array $plots = [];

    public function __construct(private Main $plugin){}

    public function claimPlot(Player $player, int $x, int $z): bool {
        $plotX = floor($x / 30);
        $plotZ = floor($z / 30);
        $key = "$plotX:$plotZ";

        if(isset($this->plots[$key])) return false;

        $this->plots[$key] = $player->getName();
        return true;
    }
}

//
// ------------------ WorldManager ------------------
//

class WorldManager implements Listener {

    private string $worldName = "PlotWorld";

    public function __construct(private Main $plugin){}

    public function loadPlotWorld(): void {

        $wm = $this->plugin->getServer()->getWorldManager();

        if(!$wm->isWorldGenerated($this->worldName)){
            $wm->generateWorld($this->worldName, "flat");
        }

        $wm->loadWorld($this->worldName);
        $world = $wm->getWorldByName($this->worldName);

        if($world instanceof World){

            // Spawn in Mitte setzen
            $world->setSpawnLocation(new Vector3(302, 65, 302));

            // Straßen generieren
            $this->generateRoads($world);

            // NPC Barriers setzen
            $world->setBlockAt(10, 65, 10, VanillaBlocks::BARRIER());
            $world->setBlockAt(12, 65, 10, VanillaBlocks::BARRIER());
            $world->setBlockAt(14, 65, 10, VanillaBlocks::BARRIER());
        }
    }

    private function generateRoads(World $world): void {

        $size = 605;
        $plotSize = 25;
        $roadWidth = 5;

        for($x = 0; $x < $size; $x++){
            for($z = 0; $z < $size; $z++){

                if($x % ($plotSize + $roadWidth) < $roadWidth ||
                   $z % ($plotSize + $roadWidth) < $roadWidth){

                    $world->setBlockAt($x, 64, $z, VanillaBlocks::OAK_PLANKS());
                    $world->setBlockAt($x, 65, $z, VanillaBlocks::SMOOTH_STONE());
                }
            }
        }
    }

    public function onPlayerInteract(PlayerInteractEvent $event): void {

        $player = $event->getPlayer();
        $pos = $event->getBlock()->getPosition();

        if($event->getBlock()->getTypeId() === VanillaBlocks::BARRIER()->getTypeId()){

            $x = $pos->getX();
            $z = $pos->getZ();
            $wm = $this->plugin->getServer()->getWorldManager();

            if($x === 10 && $z === 10){
                $this->loadExtraWorld("Farmwelt", $player);
            }

            if($x === 12 && $z === 10){
                $this->loadExtraWorld("Nether", $player);
            }

            if($x === 14 && $z === 10){
                $this->loadExtraWorld("End", $player);
            }
        }
    }

    private function loadExtraWorld(string $name, Player $player): void {

        $wm = $this->plugin->getServer()->getWorldManager();

        if(!$wm->isWorldGenerated($name)){
            $wm->generateWorld($name, "flat");
        }

        $wm->loadWorld($name);
        $world = $wm->getWorldByName($name);

        if($world instanceof World){
            $player->teleport($world->getSpawnLocation());
            $player->sendMessage("§aTeleport nach §e$name");
        }
    }
}

//
// ------------------ Economy ------------------
//

class EconomyManager {

    private array $money = [];

    public function __construct(private Main $plugin){}

    public function getMoney(Player $player): int {
        return $this->money[$player->getName()] ?? 1000;
    }

    public function addMoney(Player $player, int $amount): void {
        $this->money[$player->getName()] = $this->getMoney($player) + $amount;
    }

    public function reduceMoney(Player $player, int $amount): bool {
        if($this->getMoney($player) < $amount) return false;
        $this->money[$player->getName()] -= $amount;
        return true;
    }
}

//
// ------------------ HomeManager ------------------
//

class HomeManager {

    private array $homes = [];

    public function __construct(private Main $plugin){}

    public function setHome(Player $player, string $name): void {
        $this->homes[$player->getName()][$name] = $player->getPosition();
        $player->sendMessage("§aHome gesetzt!");
    }

    public function goHome(Player $player, string $name): void {
        if(isset($this->homes[$player->getName()][$name])){
            $player->teleport($this->homes[$player->getName()][$name]);
        }
    }

    public function delHome(Player $player, string $name): void {
        unset($this->homes[$player->getName()][$name]);
    }

    public function back(Player $player): void {}
}

class JobManager implements Listener {
    public function __construct(private Main $plugin){}
}
