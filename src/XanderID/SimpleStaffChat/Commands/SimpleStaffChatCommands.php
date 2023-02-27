<?php

declare(strict_types=1);

namespace XanderID\SimpleStaffChat\Commands;

use pocketmine\command\Command;
use pocketmine\player\Player;
use pocketmine\command\CommandSender;
use pocketmine\plugin\PluginOwned;
use pocketmine\utils\TextFormat;

use XanderID\SimpleStaffChat\SimpleStaffChat;

class SimpleStaffChatCommands extends Command implements PluginOwned {

	/** @var SimpleStaffChat $plugin */
    private $plugin;

    public function __construct(SimpleStaffChat $plugin) {
        $this->plugin = $plugin;
        parent::__construct("simplestaffchat", "a Menu for Staff Administrator", "/sschat", ["sschat", "schat", "sc"]);
        $this->setPermission("simplestaffchat.use");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : bool {
        if (!$this->testPermission($sender)) return false;
        if($this->getOwningPlugin()->setStaffChat($sender)){
        	$sender->sendMessage("§aStaffChat successfully Enabled");
        } else {
        	$sender->sendMessage("§aStaffChat successfully Disabled");
        }
        return true;
    }

    public function getOwningPlugin(): SimpleStaffChat{
        return $this->plugin;
    }    
}
