<?php

namespace CustomChat\command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginIdentifiableCommand;
use pocketmine\plugin\Plugin;
use pocketmine\utils\TextFormat;
use pocketmine\Player;
use CustomChat\Main;

class SetTagCommand extends Command implements PluginIdentifiableCommand {

    private $plugin;

    public function __construct(Main $plugin) {
        $this->plugin = $plugin;
        parent::__construct(
            "settag",
            "Sets the tag for a player",
            "/settag <player> <tag>"
        );
        $this->setPermission("customchat.settag");
    }

    public function getPlugin() : Plugin {
        return $this->plugin;
    }

    public function execute(CommandSender $sender, string $label, array $args) {
        if (!$this->testPermission($sender)) {
            return;
        }

        if (isset($args[0])) {
            if ($this->plugin->getServer()->getPlayer($args[0])) {
                if (isset($args[1])) {
                    $player = $this->plugin->getServer()->getPlayer($args[0]);
                    $config = $this->plugin->getPlayerConfig($args[0]);
                    $config->set($player->getName() . ".tags", $args[1]);
                    $config->save();
                    $this->plugin->formatPlayerDisplayName($player);
                    $sender->sendMessage(TextFormat::GREEN . "Set " . $player->getName() . "'s tag to " . $args[1]);
                } else {
                    $sender->sendMessage(TextFormat::RED . "Please enter a tag");
                }
            } else {
                $sender->sendMessage(TextFormat::RED . "Player not found");
            }
        } else {
            $sender->sendMessage(TextFormat::RED . "Please enter a player name");
        }
    }

}
