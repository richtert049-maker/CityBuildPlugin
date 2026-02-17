<?php
namespace CityBuildPlugin\commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use CityBuildPlugin\Main;

class RankCommand extends Command {
    private Main $plugin;

    public function __construct(Main $plugin){
        parent::__construct("rank","Manage ranks");
        $this->plugin=$plugin;
    }

    public function execute(CommandSender $sender,string $label,array $args): bool {
        if(!$sender instanceof Player) return false;
        if(!$sender->isOp()){ $sender->sendMessage("§cNur Operatoren!"); return false; }

        $rm = $this->plugin->getRankManager();

        if(count($args)<1){ $sender->sendMessage("§c/rank <add|remove|setperm|list>"); return false; }

        switch(strtolower($args[0])){
            case "add":
                $name=$args[1]??null; $prefix=$args[2]??"§7";
                if(!$name){ $sender->sendMessage("§c/rank add <Name> [Prefix]"); return false; }
                $sender->sendMessage($rm->addRank($name,$prefix) ? "§aRang $name erstellt!" : "§cRang existiert bereits.");
                break;
            case "remove":
                $name=$args[1]??null;
                if(!$name){ $sender->sendMessage("§c/rank remove <Name>"); return false; }
                $sender->sendMessage($rm->removeRank($name) ? "§aRang $name entfernt!" : "§cRang existiert nicht.");
                break;
            case "setperm":
                $name=$args[1]??null;
                $perms=array_slice($args,2);
                if(!$name){ $sender->sendMessage("§c/rank setperm <Name> <perm...>"); return false; }
                $sender->sendMessage($rm->setPerm($name,$perms) ? "§aPermissions gesetzt!" : "§cRang existiert nicht.");
                break;
            case "list":
                $sender->sendMessage("§eRänge:");
                foreach($rm->getAllRanks() as $rank=>$data){
                    $sender->sendMessage("§7- $rank Prefix: ".$data['prefix']." Perms: ".implode(",",$data['perms']));
                }
                break;
            default: $sender->sendMessage("§cUnbekannter Befehl."); break;
        }
        return true;
    }
}
