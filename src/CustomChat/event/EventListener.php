<?php

namespace CustomChat\event;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\level\Position;
use pocketmine\event\block\SignChangeEvent;
use pocketmine\tile\Sign;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\Player;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;
use pocketmine\Server;
use pocketmine\plugin\PluginBase;
use CustomChat\Main;
use KillChat\KillChat;
use onebone\economyapi\EconomyAPI;

class EventListener implements Listener {

    private $plugin;

    public function __construct(Main $plugin) {
        $this->plugin = $plugin;
    }

    public function onPlayerChat(PlayerChatEvent $event) {
        $allowChat = $this->plugin->config->get("disablechat");
        if ($allowChat) {
            $event->setCancelled();
            return;
        }
        if (!$allowChat || $allowChat == null) {
            $player = $event->getPlayer ();
            $perm = "chatmute";
            if ($player->isPermissionSet($perm)) {
                $event->setCancelled();
                return;
            }
            if ($this->plugin->config->get("per-world-chat") == true) {
                $format = $this->getFormattedMessage($player, $event->getMessage());
                $configNode = $this->plugin->config->get("enable-formatter");
                if (isset($configNode) && $configNode) {
                    foreach ($player->getServer()->getOnlinePlayers() as $player) {
                        if ($player->getLevel()->getName() == $player->getLevel()->getName()) {
                            $player->sendMessage($format);
                        }
                    }
                    $player->getServer()->getLogger()->info($format);
                    $event->setCancelled();
                    return;
                }
            }
            $format = $this->getFormattedMessage($player, $event->getMessage());
            $configNode = $this->plugin->config->get("enable-formatter");
            if (isset($configNode) && $configNode) {
                $event->setFormat($format);
            }
            return;
        }
    }

    public function onPlayerQuit(PlayerQuitEvent $event){ 
        $message = $this->config->get("CustomLeave");
        $player = $event->getPlayer();
        $event->setQuitMessage("");
        $message = str_replace("@Player", $event->getPlayer()->getDisplayName(), $message);
        foreach ($this->plugin->getServer()->getOnlinePlayers() as $player) {
            $player->sendPopup($message); 
        }
    }

    public function onPlayerJoin(PlayerJoinEvent $event) {
        $player = $event->getPlayer ();
        $this->plugin->formatterPlayerDisplayName($player);
        $message = $this->plugin->config->get("CustomJoin");
        $player = $event->getPlayer();
        $event->setJoinMessage("");
        $message = str_replace("@Player", $event->getPlayer()->getDisplayName(), $message);
        $this->plugin->formatterPlayerDisplayName($player);
        foreach ($this->plugin->getServer()->getOnlinePlayers() as $player) {
            $player->sendPopup($message);
        }
    }

    public function getFormattedMessage(Player $player, $message) {
        $format = $this->config->get("chat-format");
        $this->playerConfig = $this->plugin->getPlayerConfig($player->getName());
        $purePerms = $this->plugin->getServer()->getPluginManager()->getPlugin("PurePerms");
        $isMultiWorldEnabled = $purePerms->getConfig()->get("enable-multiworld-formats");
        $levelName = $isMultiWorldEnabled ?  $player->getLevel()->getName() : null;
        $format = str_replace("{PurePerms}", $purePerms->getUser($player)->getGroup($levelName)->getName(), $format);
        $format = str_replace("{Kills}", KillChat::getInstance()->getKills($player->getName()), $format); 
        $format = str_replace("{Deaths}", KillChat::getInstance()->getDeaths($player->getName()), $format); 
        $format = str_replace("{Money}", EconomyAPI::getInstance()->myMoney($player->getName()), $format); 
        $format = str_replace("{WORLD_NAME}", $player->getLevel ()->getName (), $format);
        $nick = $this->config->get($player->getName() > ".nick");
        if ($nick != null) {
            $format = str_replace("{DISPLAY_NAME}", $nick, $format);
        } else {
            $format = str_replace("{DISPLAY_NAME}", $player->getName(), $format);
        }
        $format = str_replace("{MESSAGE}", $message, $format);
        $level = $player->getLevel ()->getName();
        $tags = null;
        $playerTags = $this->playerConfig->get($player->getName() . ".tags");
        if ($playerTags != null) {
            $tags = $playerTags;
        } else {
            $tags = $this->config->get("default-player-tags");
        }
        if ($tags == null) {
            $tags = "";
        }
        $format = str_replace("{TAGS}", $tags, $format);
        $prefix = null;
        $this->playerConfig = $this->plugin->getPlayerConfig($player->getName());
        $playerPrefix = $this->playerConfig->get($player->getName() . ".prefix");
        if ($playerPrefix != null) {
            $prefix = $playerPrefix;
        } else {
            $prefix = $this->config->get("default-player-prefix");
        }
        if ($prefix == null) {
            $prefix = "";
        }
        $format = str_replace("{PREFIX}", $prefix, $format);
        return $format;
    }

}
