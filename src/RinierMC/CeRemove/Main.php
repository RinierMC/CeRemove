<?php

namespace RinierMc\CeRemove;

use RinierMC\CeRemove\Commands\CeRemove;

use pocketmine\plugin\PluginBase;
use pocketmine\Server;
use pocketmine\Player;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;

class Main extends PluginBase {

	public function onEnable(){

        //PiggyCustomEnchant
		$pce = $this->getServer()->getPluginManager()->getPlugin("PiggyCustomEnchants");
		if ($pce instanceof PiggyCustomEnchants) {
			$this->getServer()->getLogger()->notice("Load PureChat CustomEnchants!");
		} else {
			$this->getServer()->getLogger()->warning("Error no Plugin CustomEnchants!");
		}	
		
        //commands
		$this->getServer()->getCommandMap()->register("ceremove", new CeRemove("ceremove", $this));
	}
}