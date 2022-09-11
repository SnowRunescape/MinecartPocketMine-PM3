<?php

namespace Minecart\utils;

use pocketmine\network\mcpe\protocol\PlaySoundPacket;
use pocketmine\Player;
use Minecart\Minecart;

class Messages
{
    public static function getMessage(string $key) : string
    {
        return Utils::getArrayKeyByString(Minecart::getInstance()->messages, $key);
    }

    public function sendWaitingResponseInfo(Player $player)
    {
        $mode = Minecart::getInstance()->getCfg("config.waiting_response_mode");
        $info = Minecart::getInstance()->getMessage("waiting-response-info");

        switch ($mode) {
            case "title":
                $player->sendTitle($info);
                break;
            case "message":
                $player->sendMessage($info);
                break;
            case "popup":
                $player->sendPopup($info);
                break;
        }
    }

    public function sendGlobalInfo(Player $player, $type, $info)
    {
        if (!Minecart::getInstance()->getCfg("config.global_info")) {
            return;
        }

        $title = Minecart::getInstance()->getMessage("success.global-info-title");
        $subtitle = Minecart::getInstance()->getMessage($type == "vip" ? "success.global-info-subtitle-vip" : "success.global-info-subtitle-cash");

        $title = str_replace("{player.name}", $player->getName(), $title);
        $subtitle = str_replace(["{key.group}", "{cash.quantity}"], [$info, $info], $subtitle);

        Minecart::getInstance()->getServer()->broadcastTitle($title, $subtitle);

        $x = $player->getX();
        $y = $player->getY();
        $z = $player->getZ();

        $pk = new PlaySoundPacket();
        $pk->soundName = "random.levelup";
        $pk->x = $x;
        $pk->y = $y;
        $pk->z = $z;
        $pk->pitch = 1;
        $pk->volume = 300;

        $player->getLevel()->broadcastGlobalPacket($pk);
    }
}
