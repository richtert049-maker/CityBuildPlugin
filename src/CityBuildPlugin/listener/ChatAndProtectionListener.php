<?php

declare(strict_types=1);

namespace CityBuildPlugin\listener;

use CityBuildPlugin\Main;
use pocketmine\event\Listener;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerJoinEvent;

final class ChatAndProtectionListener implements Listener{

    public function __construct(private Main $plugin){}

    public function onJoin(PlayerJoinEvent $event) : void{
        $player = $event->getPlayer();
        $prefix = $this->plugin->getRankManager()->getPrefix($player);
        $player->setNameTag($prefix . " §f" . $player->getName());
    }

    public function onChat(PlayerChatEvent $event) : void{
        $player = $event->getPlayer();
        $prefix = $this->plugin->getRankManager()->getPrefix($player);
        $event->setFormat($prefix . " §f" . $player->getName() . "§8: §7" . $event->getMessage());
    }

    public function onBreak(BlockBreakEvent $event) : void{
        if($this->denyIfNoAccess($event->getPlayer()->getPosition()->getX(), $event->getPlayer()->getPosition()->getZ(), $event->getPlayer())){
            $event->cancel();
            $event->getPlayer()->sendPopup("§cDu hast hier keine Rechte.");
        }
    }

    public function onPlace(BlockPlaceEvent $event) : void{
        if($this->denyIfNoAccess($event->getPlayer()->getPosition()->getX(), $event->getPlayer()->getPosition()->getZ(), $event->getPlayer())){
            $event->cancel();
            $event->getPlayer()->sendPopup("§cDu hast hier keine Rechte.");
        }
    }

    public function onInteract(PlayerInteractEvent $event) : void{
        if($this->denyIfNoAccess($event->getPlayer()->getPosition()->getX(), $event->getPlayer()->getPosition()->getZ(), $event->getPlayer())){
            $event->cancel();
        }
    }

    private function denyIfNoAccess(float $x, float $z, $player) : bool{
        $plotManager = $this->plugin->getPlotManager();
        if(!$plotManager->isPlotWorld($player->getWorld()->getFolderName())){
            return false;
        }

        $plot = $plotManager->getPlotAt((int)$x, (int)$z);
        if($plot === null){
            return true;
        }

        $owner = $plotManager->getOwner($plot["id"]);
        if($owner === null){
            return false;
        }

        return !$plotManager->isOwnerOrTrusted($player, $plot["id"]);
    }
}
