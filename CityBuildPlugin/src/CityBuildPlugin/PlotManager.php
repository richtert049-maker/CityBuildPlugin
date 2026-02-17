<?php
namespace CityBuildPlugin;

use pocketmine\math\Vector3;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;

class PlotManager {
    private PluginBase $plugin;
    private array $plots;
    private int $plotSize = 25;

    public function __construct(PluginBase $plugin){
        $this->plugin = $plugin;
        $this->plots = $this->plugin->getConfig()->get("plots", []);
    }

    public function claimPlot(Player $player, Vector3 $pos): bool {
        $key = $this->posToKey($pos);
        if(isset($this->plots[$key])) return false;

        $this->plots[$key] = $player->getName();
        $this->plugin->getConfig()->set("plots", $this->plots);
        $this->plugin->getConfig()->save();
        return true;
    }

    public function deletePlot(Player $player, Vector3 $pos): bool {
        $key = $this->posToKey($pos);
        if(isset($this->plots[$key]) && $this->plots[$key] === $player->getName()){
            unset($this->plots[$key]);
            $this->plugin->getConfig()->set("plots", $this->plots);
            $this->plugin->getConfig()->save();
            return true;
        }
        return false;
    }

    public function canBuild(Player $player, Vector3 $pos): bool {
        foreach($this->plots as $plotKey => $owner){
            if($owner === $player->getName()){
                [$x,$y,$z] = explode(":", $plotKey);
                $x=(int)$x; $z=(int)$z;
                if($pos->x >= $x && $pos->x < $x+$this->plotSize &&
                   $pos->z >= $z && $pos->z < $z+$this->plotSize) return true;
            }
        }
        return false;
    }

    private function posToKey(Vector3 $pos): string {
        $x = floor($pos->x / $this->plotSize) * $this->plotSize;
        $z = floor($pos->z / $this->plotSize) * $this->plotSize;
        return "$x:0:$z";
    }
}
