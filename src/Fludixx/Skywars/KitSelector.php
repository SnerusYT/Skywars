<?php
namespace Fludixx\Skywars;
use pocketmine\inventory\transaction\action\SlotChangeAction;
use pocketmine\item\Item;
use pocketmine\network\mcpe\protocol\LevelEventPacket;
use pocketmine\Player;
use pocketmine\utils\TextFormat as f;
use pocketmine\utils\Config;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\EnchantmentInstance;
class KitSelector
{
	protected $plugin;

	public function __construct(Skywars $plugin)
	{
		$this->plugin = $plugin;
	}

	public function count(Player $player, int $id = Item::BRICK): int
	{
		$all = 0;
		$inv = $player->getInventory();
		$content = $inv->getContents();
		foreach ($content as $item) {
			if ($item->getId() == $id) {
				$c = $item->count;
				$all = $all + $c;
			}
		}
		return $all;
	}

	public function rm(Player $player, int $id = Item::BRICK)
	{
		$player->getInventory()->remove(Item::get($id, 0, 1));
	}

	public function add(Player $player, int $i, int $id = Item::BRICK)
	{
		$name = $player->getName();
		$inv = $player->getInventory();
		$c = 0;
		while ($c < $i) {
			$inv->addItem(
				Item::get(
					$id,
					0,
					1));
			$c++;
		}
	}

	public function setPrice(Player $player, int $price, int $id): bool
	{
		$woola = $this->count($player, $id);
		$name = $player->getName();
		if ($woola < $price) {
			$need = (int)$price - (int)$woola;
			return false;
		} else {
			$woolprice = $price;
			$wooltot = $woola - $woolprice;
			$this->rm($player, $id);
			$this->add($player, $wooltot, $id);
			return true;
		}
	}

	public function onTransaction(Player $player, Item $itemClickedOn, Item $itemClickedWith): bool
	{
		$allKits = glob("/cloud/sw/kits/".'/*.yml');
		$kitExitst = false;
		foreach($allKits as $kit) {
			$kitName = basename($kit, ".yml");
			if($itemClickedOn->getName() == f::WHITE.$kitName) {
				$kitExitst = true;
			}
		}
		if($kitExitst) {
			$player->sendMessage(Skywars::PREFIX."Du hast $kitName als Kit ausgewählt!");
			$c = new Config("/cloud/users/".$player->getName().".yml", 2);
			$c->set("kit", "$kit");
			$c->save();
			return true;
		} else {
			return false;
		}
	}
}