<?php

namespace CustomChat;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\CommandExecutor;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\utils\Config;
use pocketmine\level\Position;
use pocketmine\level\Level;
use pocketmine\level\Explosion;
use pocketmine\event\block\BlockEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\entity\EntityMoveEvent;
use pocketmine\event\entity\EntityMotionEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\block\SignChangeEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\Listener;
use pocketmine\math\Vector3 as Vector3;
use pocketmine\math\Vector2 as Vector2;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\player\PlayerRespawnEvent;
use pocketmine\event\player\PlayerLoginEvent;
use pocketmine\network\mcpe\protocol\AddMobPacket;
use pocketmine\network\mcpe\protocol\AddEntityPacket;
use pocketmine\network\mcpe\protocol\UpdateBlockPacket;
use pocketmine\block\Block;
use pocketmine\block\WallSign;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\event\server\DataPacketSendEvent;
use pocketmine\network\mcpe\protocol\DataPacket;
use pocketmine\network\mcpe\protocol\Info;
use pocketmine\network\mcpe\protocol\LoginPacket;
use pocketmine\level\generator\Generator;
use pocketmine\permission\Permission;
use CustomChat\command\CustomChatCommand;
use CustomChat\command\DisableChatCommand;
use CustomChat\command\EnableChatCommand;
use CustomChat\command\PlayerDefaultCommand;
use CustomChat\command\PrefixCommand;
use CustomChat\command\RemoveNicknameCommand;
use CustomChat\command\SetNicknameCommand;
use CustomChat\command\SetPrefixCommand;
use CustomChat\command\SetTagCommand;
use CustomChat\command\TagCommand;
use CustomChat\command\WorldCommand;
use CustomChat\event\EventListener;
use KillChat\KillChat;

class Main extends PluginBase implements CommandExecutor {

    private $path;
    private $pureperms;
    private $economyjob;
    private $playerstats;
    private static $instance = null;

    public $playerConfig;
    public $config;

    public static function getInstance() {
        return self::$instance;
    }

    public function onLoad() {
        self::$instance = $this;
    }

    public function onEnable() {
        if (!$this->getServer()->getPluginManager()->getPlugin("PurePerms") == false) {
            $this->pureperms = $this->getServer()->getPluginManager()->getPlugin("PurePerms");
        }
        if (!$this->getServer()->getPluginManager()->getPlugin("KillChat") == false) {
            $killChat = Server::getInstance()->getPluginManager()->getPlugin("KillChat");
        }
        if (!$this->getServer()->getPluginManager()->getPlugin("MassiveEconomy") == false) {
            $massiveEconomy = Server::getInstance()->getPluginManager()->getPlugin("MassiveEconomy");
        }

        $this->getServer()->getPluginManager()->addPermission(new Permission("customchat.info", "Allows player to use /customchat", Permission::DEFAULT_TRUE));
        $this->getServer()->getPluginManager()->addPermission(new Permission("customchat.disable", "Allows player to use /disablechat", Permission::DEFAULT_OP));
        $this->getServer()->getPluginManager()->addPermission(new Permission("customchat.enable", "Allows player to use /enablechat", Permission::DEFAULT_OP));
        $this->getServer()->getPluginManager()->addPermission(new Permission("customchat.prefix", "Allows player to use /prefix", Permission::DEFAULT_OP));
        $this->getServer()->getPluginManager()->addPermission(new Permission("customchat.setprefix", "Allows player to use /setprefix", Permission::DEFAULT_OP));
        $this->getServer()->getPluginManager()->addPermission(new Permission("customchat.default", "Allows player to use /default", Permission::DEFAULT_OP));
        $this->getServer()->getPluginManager()->addPermission(new Permission("customchat.setnick", "Allows player to use /setnick", Permission::DEFAULT_OP));
        $this->getServer()->getPluginManager()->addPermission(new Permission("customchat.tag", "Allows player to use /tag", Permission::DEFAULT_OP));
        $this->getServer()->getPluginManager()->addPermission(new Permission("customchat.settag", "Allows player to use /settag", Permission::DEFAULT_OP));
        $this->getServer()->getPluginManager()->addPermission(new Permission("customchat.world", "Allows player to use /world", Permission::DEFAULT_OP));

        $this->getServer()->getCommandMap()->register("customchat", new CustomChatCommand($this));
        $this->getServer()->getCommandMap()->register("disablechat", new DisableChatCommand($this));
        $this->getServer()->getCommandMap()->register("enablechat", new EnableChatCommand($this));
        $this->getServer()->getCommandMap()->register("default", new PlayerDefaultCommand($this));
        $this->getServer()->getCommandMap()->register("prefix", new PrefixCommand($this));
        $this->getServer()->getCommandMap()->register("removenick", new RemoveNicknameCommand($this));
        $this->getServer()->getCommandMap()->register("setnick", new SetNicknameCommand($this));
        $this->getServer()->getCommandMap()->register("setprefix", new SetPrefixCommand($this));
        $this->getServer()->getCommandMap()->register("settag", new SetTagCommand($this));
        $this->getServer()->getCommandMap()->register("world", new WorldCommand($this));

        $this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
        @mkdir($this->getDataFolder());
        @mkdir($this->getDataFolder() . "players/");
        $this->path = $this->getDataFolder(); 
        if (!is_file($this->path . "config.yml")) {
            file_put_contents($this->path . "config.yml", $this->readResource("config.yml"));
        }
        $this->config = new Config($this->path . "config.yml", Config::YAML);
    }

    public function onDisable() {

    }

    public function formatPlayerDisplayName(Player $player) {
        $prefix = null;
        $this->playerConfig = new Config($this->path . "players/" . $player->getName() . ".yml", Config::YAML);
        $playerPrefix = $this->playerConfig->get($player->getName() . ".prefix");
        if ($playerPrefix != null) {
            $prefix = $playerPrefix;
        } else {
            $prefix = $this->config->get("default-player-prefix");
        }
        $nick = $this->playerConfig->get($player->getName() . ".nick");
        if ($nick != null && $prefix != null) {
            $player->setNameTag( $prefix . ":" . $nick );
            return;
        }
        if ($nick != null && $prefix == null) {
            $player->setNameTag($nick );
            return;
        }
        if ($nick == null && $prefix != null) {
            $player->setNameTag($prefix . ":".$player->getName());
            return;
        }
        $player->setNameTag($player->getName());
    }

    private function readResource($file) {
        $resource = $this->getResource($file);
        if ($resource !== null) {
            return stream_get_contents($resource);
        }
        return false;
    }

    public function getPlayerConfig($player) {
        if ($player instanceof Player) {
            return new Config($this->getDataFolder() . "players/" . $player->getName() . ".yml");
        }
        return new Config($this->getDataFolder() . "players/" . $player . ".yml");
    }

}
