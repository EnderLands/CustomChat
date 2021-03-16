<?php

namespace CustomChat\command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginIdentifiableCommand;
use pocketmine\plugin\Plugin;
use pocketmine\utils\TextFormat;
use pocketmine\Player;
use CustomChat\Main;

class EnableChatCommand extends Command implements PluginIdentifiableCommand {

    private $plugin;

    public function __construct(Main $plugin) {
        $this->plugin = $plugin;
        parent::__construct(
            "enablechat",
            "Enable chat for all players",
            "/enablechat"
        );
        $this->setPermission("customchat.enable");
    }

    public function getPlugin() : Plugin {
        return $this->plugin;
    }

    public function execute(CommandSender $sender, string $label, array $args) {
        if (!$this->testPermission($sender)) {
            return;
        }

        $this->plugin->config->set("disablechat", false);
        $this->plugin->config->save();
        $sender->sendMessage(TextFormat::GREEN . "Enabled chat for all players");
        $this->plugin->plugin->getLogger()->info("Enabled chat for all players");
    }

}
