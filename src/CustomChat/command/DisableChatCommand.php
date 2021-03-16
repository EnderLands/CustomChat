<?php

namespace CustomChat\command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginIdentifiableCommand;
use pocketmine\plugin\Plugin;
use pocketmine\utils\TextFormat;
use pocketmine\Player;
use CustomChat\Main;

class DisableChatCommand extends Command implements PluginIdentifiableCommand {

    private $plugin;

    public function __construct(Main $plugin) {
        $this->plugin = $plugin;
        parent::__construct(
            "disablechat",
            "Disable chat for all players",
            "/disablechat"
        );
        $this->setPermission("customchat.disable");
    }

    public function getPlugin() : Plugin {
        return $this->plugin;
    }

    public function execute(CommandSender $sender, string $label, array $args) {
        if (!$this->testPermission($sender)) {
            return;
        }

        $this->plugin->config->set("disablechat", true);
        $this->plugin->config->save();
        $sender->sendMessage(TextFormat::RED . "Disabled chat for all players");
        $this->plugin->getLogger()->info("Disabled chat for all players");
    }

}
