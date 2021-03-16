<?php

namespace CustomChat\command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginIdentifiableCommand;
use pocketmine\plugin\Plugin;
use pocketmine\utils\TextFormat;
use pocketmine\Player;
use CustomChat\Main;

class CustomChatCommand extends Command implements PluginIdentifiableCommand {

    private $plugin;

    public function __construct(Main $plugin) {
        $this->plugin = $plugin;
        parent::__construct(
            "customchat",
            "CustomChat Features",
            "/customchat"
        );
        $this->setPermission("customchat.info");
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
                case "help":
                case "options":
                    $sender->sendMessage(TextFormat::WHITE . "-==[ CustomChat Info ]==-");
                    $sender->sendMessage(TextFormat::WHITE . "PurePerms use :{PurePerms}");
                    $sender->sendMessage(TextFormat::WHITE . "MassiveEconomy use :{Money}");
                    $sender->sendMessage(TextFormat::WHITE . "KillChat use :{Kill}");
                    $sender->sendMessage(TextFormat::WHITE . "KillChat use :{Deaths}");
                    break;
                default:
                    break;
            }
        } else {
            $sender->sendMessage(TextFormat::RED . "-==[ CustomChat Info ]==-");
            $sender->sendMessage(TextFormat::RED . "Usage: /customchat");
            $sender->sendMessage(TextFormat::RED . "Usage: /enablechat");
            $sender->sendMessage(TextFormat::RED . "Usage: /disablechat");
            $sender->sendMessage(TextFormat::RED . "Usage: /default <prefix|tag> <player>");
            $sender->sendMessage(TextFormat::RED . "Usage: /prefix <prefix>");
            $sender->sendMessage(TextFormat::RED . "Usage: /removenick <player>");
            $sender->sendMessage(TextFormat::RED . "Usage: /setnick <player> <nickname>");
            $sender->sendMessage(TextFormat::RED . "Usage: /setprefix <player> <prefix>");
            $sender->sendMessage(TextFormat::RED . "Usage: /settag <player> <tag>");
            $sender->sendMessage(TextFormat::RED . "Usage: /tag <tag>");
            $sender->sendMessage(TextFormat::RED . "Usage: /world <enable|disable>");
        }
    }

}
