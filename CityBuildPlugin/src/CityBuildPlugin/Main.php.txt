<?php
namespace CityBuildPlugin;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\player\Player;

class Main extends PluginBase implements Listener {
    private PlotManager $plotManager;
    private RankManager $rankManager;

    public function onEnable(): void {
        $this->saveDefaultConfig();

        $this->plotManager = new PlotManager($this);
        $this->rankManager = new RankManager($this);

        $this->getServer()->getCommandMap()->register("plot", new commands\PlotCommand($this));
        $this->getServer()->getCommandMap()->register("spawn", new commands\SpawnCommand($this));
        $this->getServer()->getCommandMap()->register("rank", new commands\RankCommand($this));

        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->setupMainWorld();

        $this->getLogger()->info("CityBuildPlugin aktiviert!");
    }

    public function getPlotManager(): PlotManager { return $this->plotManager; }
    public function getRankManager(): RankManager { return $this->rankManager; }

    public function onBlockPlace(BlockPlaceEvent $event): void {
        $player = $event->getPlayer();
        if(!$this->plotManager->canBuild($player, $event->getBlock()->getPosition())){
            $event->cancel();
            $player->sendMessage("§cDu kannst hier nicht bauen!");
        }
    }

    public function onBlockBreak(BlockBreakEvent $event): void {
        $player = $event->getPlayer();
        if(!$this->plotManager->canBuild($player, $event->getBlock()->getPosition())){
            $event->cancel();
            $player->sendMessage("§cDu kannst hier nicht abbauen!");
        }
    }

    private function setupMainWorld(): void {
        $world = $this->getServer()->getWorldManager()->getDefaultWorld();
        if($world !== null){
            $world->setSpawnLocation(1250, 65, 1250);
        }
    }
}
