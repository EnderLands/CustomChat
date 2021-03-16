<?php

namespace CustomChat\command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginIdentifiableCommand;
use pocketmine\plugin\Plugin;
use pocketmine\utils\TextFormat;
use pocketmine\Player;
use CustomChat\Main;

class WorldCommand extends Command implements PluginIdentifiableCommand {

    private $plugin;

    public function __construct(Main $plugin) {
        $this->plugin = $plugin;
        parent::__construct(
            "world",
            "Enable or disable PerWorldChat for all players",
            "/world <enable|disable>",
            ["worldchat"]
        );
        $this->setPermission("customchat.world");
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
                case "enable":
                case "on":
                    $this->plugin->config->set("per-world-chat", true);
                    $this->plugin->config->save();
                    $sender->sendMessage(TextFormat::GREEN . "Enabled PerWorldChat");
                    break;
                case "disable":
                case "off":
                    $this->plugin->config->set("per-world-chat", false);
                    $this->plugin->config->save();
                    $sender->sendMessage(TextFormat::GREEN . "Disabled PerWorldChat");
                    break;
                default:
                    if ($this->plugin->config->get("per-world-chat") == false) {
                        $this->plugin->config->set("per-world-chat", true);
                        $this->plugin->config->save();
                        $sender->sendMessage(TextFormat::GREEN . "Enabled PerWorldChat");
                    } elseif ($this->plugin->config->get("per-world-chat") == true) {
                        $this->plugin->config->set("per-world-chat", false);
                        $this->plugin->config->save();
                        $sender->sendMessage(TextFormat::GREEN . "Disabled PerWorldChat");
                    }
                    break;
            }
        } else {
            if ($this->plugin->config->get("per-world-chat") == false) {
                $this->plugin->config->set("per-world-chat", true);
                $this->plugin->config->save();
                $sender->sendMessage(TextFormat::GREEN . "Enabled PerWorldChat");
            } elseif ($this->plugin->config->get("per-world-chat") == true) {
                $this->plugin->config->set("per-world-chat", false);
                $this->plugin->config->save();
                $sender->sendMessage(TextFormat::GREEN . "Disabled PerWorldChat");
            }
        }
    }

}
