<?php

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginIdentifiableCommand;
use pocketmine\plugin\Plugin;
use pocketmine\utils\TextFormat;
use pocketmine\Player;
use CustomChat\Main;

class RemoveNicknameCommand extends Command implements PluginIdentifiableCommand {

    private $plugin;

    public function __construct(Main $plugin) {
        $this->plugin = $plugin;
        parent::__construct(
            "removenick",
            "Removes a player's nickname",
            "/removenick <player>",
            ["removenickname", "deletenick", "deletenickname"]
        );
        $this->setPermission("customchat.removenick");
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
                $player = $this->plugin->getServer()->getPlayer($args[0]);
                $config = $this->plugin->getPlayerConfig($player);
                $config->remove($player->getName() . ".nick");
                $config->save();
                $this->plugin->formatPlayerDisplayName($player);
                $sender->sendMessage(TextFormat::GREEN . "Removed " . $player->getName() . "'s nickname");
            } else {
                $sender->sendMessage(TextFormat::RED . "Player not found");
            }
        } else {
            $sender->sendMessage(TextFormat::RED . "Please enter a player name");
        }
    }

}
