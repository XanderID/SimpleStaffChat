<?php

declare(strict_types=1);

namespace XanderID\SimpleStaffChat;

use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;

use pocketmine\console\ConsoleCommandSender;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\lang\Language;

use XanderID\SimpleStaffChat\Commands\SimpleStaffChatCommands;

class SimpleStaffChat extends PluginBase implements Listener {
	
	/** @param array $staffchat */
	private $staffchat = [];

    public function onEnable(): void {
        $this->saveDefaultConfig();
        $this->getServer()->getCommandMap()->register("simplestaffchat", new SimpleStaffChatCommands($this));
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }
    
    /** 
    * @param bool $online
    *
	* @return array
	*/
    public function getStaffReception(bool $online = false): array{
    	// If online false it will give Player Name
    	if(!$online){
    		return $this->staffchat;
    	}
    	$p = [];
    	foreach($this->staffchat as $staff => $noneed){
    		$player = $this->getServer()->getPlayerByPrefix($staff);
    		if($player !== null){
    			$p[] = $player;
    		}
    	}
    	return $p;
    }
    
    /**
    * @param Player|string $player
    * @return bool
	*/
	public function isChatStaff(Player|string $player): bool{
		if($player instanceof Player) $player = $player->getName();
		return in_array($player, $this->getStaffReception());
	}
	
	/**
	* @param Player|string $player
    * @return bool
	*/
	public function setStaffChat(Player|string $player): bool{
		if($player instanceof Player) $player = $player->getName();
		if($this->isChatStaff($player)){
			unset($this->staffchat[$player]);
			return false;
		} else {
			$this->staffchat[$player] = true;
			return true;
		}
	}
	
	/**
	* @param string $chat
	* @return bool
	*/
	public function checkPrefix(string $chat): bool{
		$prefix = substr($chat, 0, strlen($this->getConfig()->get("prefix-chat"))); // Check Prefix on Chat
		if($prefix !== $this->getConfig()->get("prefix-chat")){
			return false;
		}
		return true;
	}

    /**
     * @priority HIGHEST
    * @param PlayerChatEvent $event
    */
    public function onChat(PlayerChatEvent $event): bool{
    	if($event->isCancelled()) return false;
    	$player = $event->getPlayer(); 
    	if(!$player->hasPermission("simplestaffchat.use")) return false;
    	$chat = $event->getMessage();
    	
    	if(!$this->checkPrefix($chat) && !$this->isChatStaff($player)) return false;
    	if($this->checkPrefix($chat)){
    		$chat = substr($chat, strlen($this->getConfig()->get("prefix-chat"))); // Crop the prefix on chat
		}
    	$format = str_replace(["{player}", "{chat}"], [$player->getName(), $chat], $this->getConfig()->get("format"));
    	$recipients = $this->getStaffReception(true);
    	if(!$this->isChatStaff($player)){// Why add this? if player use prefix the message will not go to yourself
    		array_push($recipients, $player);
    	}
    	array_push($recipients, new ConsoleCommandSender($this->getServer(), new Language("eng"))); // Why use this? otherwise the console will not accept SimpleStaffChat
		$event->setFormat($format);
		$event->setRecipients($recipients);
		return true;
    }
}
