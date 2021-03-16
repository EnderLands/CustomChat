<?php

namespace CustomChat\command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginIdentifiableCommand;
use pocketmine\plugin\Plugin;
use pocketmine\utils\TextFormat;
use pocketmine\Player;
use CustomChat\Main;

class TagCommand extends Command implements PluginIdentifiableCommand {

    private $plugin;

    public function __construct(Main $plugin) {
        $this->plugin = $plugin;
        parent::__construct(
            "tag",
            "Sets the default tag",
            "/tag <tag>"
        );
        $this->setPermission("customchat.tag");
    }

    public function getPlugin() : Plugin {
        return $this->plugin;
    }

    public function execute(CommandSender $sender, string $label, array $args) {
        if (!$this->testPermission($sender)) {
            return;
        }

        if (isset($args[0])) {
            $this->plugin->config->set("default-player-tags", $args[0]);
            $this->plugin->save();
            $sender->sendMessage(TextFormat::GREEN . "Successfully set default prefix to " . $args[0]);
        } else {
            $sender->sendMessage(TextFormat::RED . "Please enter a tag");
        }
    }

}
