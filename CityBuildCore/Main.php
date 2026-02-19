<?php

namespace CityBuildCore;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

// Manager & Utils laden
require_once __DIR__ . "/Managers.php";
require_once __DIR__ . "/Utils.php";

class Main extends PluginBase implements Listener {

    public PlotManager $plotManager;
    public EconomyManager $economyManager;
    public HomeManager $homeManager;
    public WorldManager $worldManager;
    public ClearLagg $clearLagg;
    public ScoreboardManager $scoreboardManager;

    public function onEnable(): void {
        $this->getLogger()->info("§aCityBuildCore final Version geladen!");

        // Manager initialisieren
        $this->plotManager = new PlotManager($this);
        $this->economyManager = new EconomyManager($this);
        $this->homeManager = new HomeManager($this);
        $this->worldManager = new WorldManager($this);
        $this->clearLagg = new ClearLagg($this);
        $this->scoreboardManager = new ScoreboardManager($this);
        $this->jobManager = new JobManager($this);

        // Events registrieren
        $pm = $this->getServer()->getPluginManager();
        $pm->registerEvents($this->plotManager, $this);
        $pm->registerEvents($this->homeManager, $this);
        $pm->registerEvents($this->worldManager, $this);
        $pm->registerEvents($this->jobManager, $this);

        // Start Clearlagg & Scoreboard
        $this->clearLagg->start();
        $this->scoreboardManager->start();

        // Plotwelt + Straßen generieren
        $this->worldManager->loadPlotWorld();
        $this->worldManager->generateRoads();
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool {
        if(!$sender instanceof Player) return false;

        switch(strtolower($command->getName())) {

            case "money":
                $sender->sendMessage("§aDein Guthaben: §e".$this->economyManager->getMoney($sender)." Coins");
                return true;

            case "pay":
                if(count($args) < 2){
                    $sender->sendMessage("§cBenutzung: /pay <Spieler> <Betrag>");
                    return false;
                }
                $target = $this->getServer()->getPlayerByPrefix($args[0]);
                $amount = (int)$args[1];
                if($target === null){
                    $sender->sendMessage("§cSpieler nicht gefunden!");
                    return false;
                }
                if($amount <= 0){
                    $sender->sendMessage("§cBetrag muss größer 0 sein!");
                    return false;
                }
                if(!$this->economyManager->reduceMoney($sender, $amount)){
                    $sender->sendMessage("§cDu hast nicht genug Coins!");
                    return false;
                }
                $this->economyManager->addMoney($target, $amount);
                $sender->sendMessage("§aDu hast §e$amount Coins §aan §e".$target->getName());
                $target->sendMessage("§aDu hast §e$amount Coins §avon §e".$sender->getName());
                return true;

            case "claimplot":
                $pos = $sender->getPosition();
                if($this->plotManager->claimPlot($sender, (int)$pos->getX(), (int)$pos->getZ())){
                    $sender->sendMessage("§aPlot erfolgreich geclaimt!");
                } else {
                    $sender->sendMessage("§cDu hast nicht genug Coins oder der Plot ist vergeben!");
                }
                return true;

            case "sethome":
                $this->homeManager->setHome($sender, $args[0] ?? "home");
                return true;

            case "home":
                $this->homeManager->goHome($sender, $args[0] ?? "home");
                return true;

            case "delhome":
                $this->homeManager->delHome($sender, $args[0] ?? "home");
                return true;

            case "back":
                $this->homeManager->back($sender);
                return true;
        }

        return false;
    }
}
