<?php

namespace CustomChat\command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginIdentifiableCommand;
use pocketmine\plugin\Plugin;
use pocketmine\utils\TextFormat;
use pocketmine\Player;
use CustomChat\Main;

class PlayerDefaultCommand extends Command implements PluginIdentifiableCommand {

    private $plugin;

    public function __construct(Main $plugin) {
        $this->plugin = $plugin;
        parent::__construct(
            "default",
            "Sets a player's prefix or tag to default",
            "/default <type: prefix|tag> <player>"
        );
        $this->setPermission("customchat.default");
    }

    public function getPlugin() : Plugin {
        return $this->plugin;
    }

    public function execute(CommandSender $sender, string $label, array $args) {
        if (!$this->testPermission($sender)) {
            return;
        }

        if (isset($args[0])) {
            switch ($args[0]) {
                case "prefix":
                    if (isset($args[1])) {
                        if ($this->plugin->getServer()->getPlayer($args[1])) {
                            $player = $this->plugin->getServer()->getPlayer($args[1]);
                            $config = $this->plugin->getPlayerConfig($player);
                            $config->set($player->getName() . ".prefix", $this->plugin->config->get("default-player-prefix"));
                            $config->save();
                            $sender->sendMessage(TextFormat::GREEN . "Set " . $player->getName() . "'s prefix to default");
                        } else {
                            $sender->sendMessage(TextFormat::RED . "Player not found");
                        }
                    } else {
                        $sender->sendMessage(TextFormat::RED . "Please enter a player name");
                    }
                    break;
                case "tag":
                    if (isset($args[1])) {
                        if ($this->plugin->getServer()->getPlayer($args[1])) {
                            $player = $this->plugin->getServer()->getPlayer($args[1]);
                            $config = $this->plugin->getPlayerConfig($player);
                            $config->set($player->getName() . ".tags", $this->plugin->config->get("default-player-tags"));
                            $config->save();
                            $sender->sendMessage(TextFormat::GREEN . "Set " . $player->getName() . "'s tag to default");
                        } else {
                            $sender->sendMessage(TextFormat::RED . "Player not found");
                        }
                    } else {
                        $sender->sendMessage(TextFormat::RED . "Please enter a player name");
                    }
                    break;
                default:
                    $sender->sendMessage(TextFormat::RED . "Invalid type");
                    break;
            }
        } else {
            $sender->sendMessage(TextFormat::RED . "Please enter a type");
        }
    }

}
