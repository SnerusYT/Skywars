<?php

namespace Fludixx\Skywars;

use pocketmine\entity\object\ItemEntity;
use pocketmine\item\Item;
use pocketmine\Server;
use Fludixx\Skywars\Skywars;
use pocketmine\scheduler\Task;
use pocketmine\tile\Chest;
use pocketmine\utils\Config;
use pocketmine\level\Level;
use pocketmine\tile\Sign;
use pocketmine\utils\TextFormat as f;
use pocketmine\level\Position;

class SwCountdown extends Task
{
	public $plugin;
	public $level;
	public $min;

	public function __construct(Skywars $plugin, Level $level, int $min)
	{

		/**
		 * @param Skywars $plugin
		 * @param Level $level
		 */

		$this->plugin = $plugin;
		$this->level = $level;
		$this->min = $min;
	}

	public function onRun(int $tick)
	{
		$name = $this->level->getFolderName();
		$c = new Config("/cloud/sw/$name.yml", Config::YAML);
		$cd = (int)$c->get("countdown");
		$cd = $cd - 1;
		$c->set("countdown", $cd);
		$c->save();
		$time = $c->get("countdown");
		$players = $this->plugin->getServer()->getOnlinePlayers();
		$counter = 0;
		foreach ($players as $player) {
			if ($player->getLevel()->getFolderName() == $name) {
				$counter++;
				$player->setXpLevel((int)$time);
				$xpbar = (double)bcmul((string)bcdiv((string)1, (string)60, 6), (string)$time, 6);
				$player->setXpProgress($xpbar);
			}
		}
		if ($time == 30) {
			$players = $this->plugin->getServer()->getOnlinePlayers();
			foreach ($players as $player) {
				if ($player->getLevel()->getFolderName() == $name) {
					$player->sendMessage($this->plugin->prefix . "Noch 30 Sekunden!");
				}
			}
		}
		if ($time == 10) {
			$players = $this->plugin->getServer()->getOnlinePlayers();
			foreach ($players as $player) {
				if ($player->getLevel()->getFolderName() == $name) {
					$player->sendMessage($this->plugin->prefix . "Noch 10 Sekunden!");
				}
			}
		}
		if ($time == 5) {
			$players = $this->plugin->getServer()->getOnlinePlayers();
			foreach ($players as $player) {
				if ($player->getLevel()->getFolderName() == $name) {
					$player->sendMessage($this->plugin->prefix . "Noch 5 Sekunden!");
				}
			}
		}
		if ($counter < $this->min) {
			$players = $this->plugin->getServer()->getOnlinePlayers();
			foreach ($players as $player) {
				if ($player->getLevel()->getFolderName() == $name) {
					$player->sendMessage($this->plugin->prefix . "Countdown wurde unterbrochen! Zuwenige Spieler.");
					$this->plugin->getScheduler()->cancelTask($this->getTaskId());
				}
			}
		}
		if ($time == 1) {
			$players = $this->plugin->getServer()->getOnlinePlayers();
			$teamint = 1;
			$teamdurchlauf = 0;
			foreach ($players as $player) {
				if ($player->getLevel()->getFolderName() == $name) {
					// TEAM CODE
					$dimension = (string)$c->get("dimension");
					$playerProTeam = (int)substr($dimension, -1);
					$allTeams = $dimension[0];
					$cp = new Config("/cloud/users/".$player->getName().".yml", 2);
					$pname = $player->getName();
					if($cp->get("team") != false) {
						$cp->set("pos", $cp->get("team"));
						$cp->set("bett", true);
						$cp->save();
					} else {
						$players = $this->plugin->getServer()->getOnlinePlayers();
						$t1 = 0;
						$t2 = 0;
						$t3 = 0;
						$t4 = 0;
						$t5 = 0;
						$t6 = 0;
						$t7 = 0;
						$t8 = 0;
						foreach($players as $person) {
							if($person->getLevel()->getFolderName() == $this->level->getFolderName()) {
								$pc = new Config("/cloud/users/".$person->getName().".yml", 2);
								for($currentTeam = 1; $currentTeam-1 == $allTeams, $currentTeam++;) {
									if($currentTeam-1 == $allTeams) {
										break;
									}
									$this->plugin->getLogger()->info($currentTeam-1);
									if($pc->get("team") == (int)$currentTeam-1) {
										$var = (string)"t".(int)$currentTeam - (int)1;
										$$var++;
										$this->plugin->getLogger()->info($$var);
									}
								}
							}
						}
						if($t1 != $playerProTeam) {
							$cp->set("team", 1);
							$cp->save();
						}
						if($t2 != $playerProTeam) {
							$cp->set("team", 2);
							$cp->save();
						}
						if($t3 != $playerProTeam) {
							$cp->set("team", 3);
							$cp->save();
						}
						if($t4 != $playerProTeam) {
							$cp->set("team", 4);
							$cp->save();
						}
						if($t5 != $playerProTeam) {
							$cp->set("team", 5);
							$cp->save();
						}
						if($t6 != $playerProTeam) {
							$cp->set("team", 6);
							$cp->save();
						}
						if($t7 != $playerProTeam) {
							$cp->set("team", 7);
							$cp->save();
						}
						if($t8 != $playerProTeam) {
							$cp->set("team", 8);
							$cp->save();
						}
						$cp->set("pos", $cp->get("team"));
						$cp->save();
					}
					$player->sendMessage(f::BOLD . f::GREEN . "Das Spiel beginnt!");
					$pos = $cp->get("pos");
					$spawn = $c->get("p$pos");
					$pos = new Position($spawn[0], $spawn[1], $spawn[2], $this->level);
					$player->teleport($pos);
					$player->setGamemode(0);
					$this->plugin->getEq($player);
					$this->plugin->getScheduler()->scheduleRepeatingTask(new SwAsker($this->plugin, $player), 5);
					$this->plugin->getLogger()->info("Asker Task hat den Wert '$pname' bekommen.");
				}

			}
			$c->set("busy", true);
			$c->save();
			$items = $this->level->getEntities();
			foreach($items as $item) {
				if($item instanceof ItemEntity || $item instanceof Item) {
					$item->despawnFromAll();
					$item->kill();
				}
			}
			$blocks = array(5, 43, 3, 17, 24, 30, 47, 78, 87, 95);
			$items = array(264, 265, 280, 288, 320, 322, 332, 397, 366, 368, 396);
			$tools = array(267, 268, 269, 270, 271, 272, 273, 274, 275, 276, 278, 279, 307, 303, 311, 304, 308, 312,
				309, 305, 306, 314, 302, 306);
			$tiles = $this->level->getTiles();
			foreach($tiles as $chest) {
				if($chest instanceof Chest) {
					$inv = $chest->getInventory();
					$inv->clearAll();
					$slot1 = mt_rand(0, 20);$slot2 = mt_rand(0, 20);$slot3 = mt_rand(0, 20);
					$slot4 = mt_rand(0, 20);$slot5 = mt_rand(0, 20);$slot6 = mt_rand(0, 20);
					$slot7 = mt_rand(0, 20);

					$key1 = array_rand($blocks);$key2 = array_rand($blocks);$key3 = array_rand($items);
					$key4 = array_rand($items);$key5 = array_rand($items);$key6 = array_rand($tools);
					$key7 = array_rand($tools);
					$maxItems = array(1, 2, 3, 4, 5, 6, 7);
					$inv->setItem($slot1, Item::get($blocks[$key1], 32, mt_rand(1, 64)));$inv->setItem($slot2, Item::get($blocks[$key2], 0, mt_rand(32, 64)));
					$inv->setItem($slot3, Item::get($items[$key3], 0, mt_rand(1, 18)));$inv->setItem($slot4, Item::get($items[$key4], 0, mt_rand(1, 15)));
					$inv->setItem($slot5, Item::get($items[$key5], 0, mt_rand(1, 16)));$inv->setItem($slot6, Item::get($tools[$key6]));
					$inv->setItem($slot7, Item::get($tools[$key7]));
				}
			}

			$this->plugin->getScheduler()->cancelTask($this->getTaskId());

		}

	}
}
