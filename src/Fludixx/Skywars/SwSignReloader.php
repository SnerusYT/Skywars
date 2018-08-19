<?php
namespace Fludixx\Skywars;

use pocketmine\item\Bed;
use pocketmine\math\Vector3;
use Fludixx\Skywars\Skywars;
use pocketmine\scheduler\Task;
use pocketmine\utils\Config;
use pocketmine\level\Level;
use pocketmine\tile\Sign;
use pocketmine\utils\TextFormat as f;
use pocketmine\level\Position;

class SwSignReloader extends Task
{
	public $plugin;
	public $level;

	public function __construct(Skywars $plugin, Level $level)
	{

		/**
		 * @param Skywars $plugin
		 * @param Level $level
		 */

		$this->plugin = $plugin;
		$this->level = $level;
	}

	public function onRun(int $tick)
	{
		$levelname = $this->level->getName();
		$this->plugin->getLogger()->info(Skywars::PREFIX . "Reloade Signs auf: $levelname...");
		$tiles = $this->plugin->getServer()->getDefaultLevel()->getTiles();
		foreach ($tiles as $tile) {
			if ($tile instanceof \pocketmine\tile\Sign) {
				$text = $tile->getText();
				if ($text[0] == Skywars::NAME || $text[0] == f::RED . "Skywars") {
					$this->plugin->getScheduler()->scheduleRepeatingTask(new SwSignUpdater($this->plugin, $tile), 20);
					$this->plugin->getLogger()->info("Schild wurde Reloaded!");
				}
			}
		}

	}
}