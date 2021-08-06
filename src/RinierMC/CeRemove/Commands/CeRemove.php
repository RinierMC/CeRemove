<?php

namespace RinierMC\CeRemove\Commands;

use RinierMC\CeRemove\Main;

use pocketmine\Server;
use pocketmine\Player;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginCommand;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\Item;
use pocketmine\utils\TextFormat as TF;

use DaPigGuy\PiggyCustomEnchants\enchants\CustomEnchant;
use DaPigGuy\PiggyCustomEnchants\PiggyCustomEnchants;
use DaPigGuy\PiggyCustomEnchants\CustomEnchantManager;

class CeRemove extends PluginCommand{

    public function __construct(string $name, Main $plugin)
    {
        parent::__construct($name, $plugin);
        $this->plugin = $plugin;
        $this->setPermission("cr.command");
        $this->setUsage("/ceremove <enchant>");
        $this->setDescription("Transfer a custom enchant from your item into a book");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if($sender->hasPermission($this->getPermission()) and $sender instanceof Player){
            $cost = 20000;
            if ($sender->getCurrentTotalXp() - $cost < 0) {
                $sender->sendMessage("§7§l(§c!§7) §aYou don't have enough EXP");
                $sender->sendMessage("§l§7(§c!§7) §aYou Need 20,000 EXP");
            } else {
                if(isset($args[0])){
                    $ench = CustomEnchantManager::getEnchantmentByName($args[0]);
                    if($ench !== null){
                        $item = $sender->getInventory()->getItemInHand();
                        if($item->getEnchantment($ench->getId()) !== null){
                            $level = $item->getEnchantment($ench->getId())->getLevel();
                            $ebook = Item::get(403, 0, 1);
                            $ebook->setCustomName(TF::BOLD . TF::BLUE . $args[0]);	
                            $ebook->setLore(["§r\n§7CERemoved Book §r\n§7Name: §l§e$args[0] §r\n§7Level: §l§a$level\n§r "]);
                            $piggy = $this->plugin->getServer()->getPluginManager()->getPlugin("PiggyCustomEnchants");
                            if($piggy instanceof PiggyCustomEnchants) {
                                $ebook->addEnchantment(new EnchantmentInstance(new CustomEnchant($piggy, $ench->getId(), $ench->getRarity()), $level));
                                if($sender->getInventory()->canAddItem($ebook)) {
                                    $newItem = clone $item;
                                    $newItem->removeEnchantment($ench->getId());
                                    $inv = $sender->getInventory();
                                    $inv->setItemInHand($newItem);
                                    $inv->addItem($ebook);
									$sender->subtractXp($cost);
                                    $sender->sendMessage(TF::GREEN . "Enchantment " . $args[0] . " was successfully separated into a book from " . $item->getName());
                                }
                                else {
                                    $sender->sendMessage(TF::RED . "You do not have enough space in your inventory to collect the enchantment book");
                                }
                            }
                            else {
                                $sender->sendMessage(TF::LIGHT_PURPLE . "This error was no supposed to occur, contact an Owner as soon as possible");
                            }
                        }
                        else {
                            $sender->sendMessage(TF::RED . "You do not have that enchantment on your currently held item");
                        }
                    }
                    else {
                        $sender->sendMessage(TF::RED . "Such enchantment does not exist");
                    }
                }
                else {
                    $sender->sendMessage("§7§l(§a!§7) §aUsage §e/ceremove <CustomEnchant>");
                }
            }
			return true;
        }
    }
}