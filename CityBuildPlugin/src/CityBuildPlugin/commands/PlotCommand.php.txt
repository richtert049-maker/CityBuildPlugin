<?php
namespace CityBuildPlugin\commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use CityBuildPlugin\Main;
use pocketmine\math\Vector3;

class PlotCommand extends Command {
    private Main $plugin;

    public function __construct(Main $plugin){
        parent::__construct("plot","Manage plots");
        $this->plugin = $plugin;
    }

    public function execute(CommandSender $sender,string $label,array $args): bool {
        if(!$sender instanceof Player) return false;
        $pm = $this->plugin->getPlotManager();

        if(count($args)<1){
            $sender->sendMessage("§c/plot <claim|tp|delete>");
            return false;
        }

        $action = strtolower($args[0]);
        $pos = $sender->getPosition();

        switch($action){
            case "claim":
                $success = $pm->claimPlot($sender,$pos);
                $sender->sendMessage($success ? "§aPlot beansprucht!" : "§cPlot existiert bereits.");
                break;
            case "delete":
                $success = $pm->deletePlot($sender,$pos);
                $sender->sendMessage($success ? "§aPlot gelöscht!" : "§cDu besitzt hier keinen Plot.");
                break;
            case "tp":
                $sender->teleport($pos); // Einfaches Teleport zu Plot (kann erweitert werden)
                $sender->sendMessage("§eTeleported to your plot!");
                break;
            default:
                $sender->sendMessage("§cUnbekannter Befehl. /plot <claim|tp|delete>");
                break;
        }
        return true;
    }
}
