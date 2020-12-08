<?php

namespace App\AppBundle;

class AdminMethods {

    private $minPowerPanel = 2;
    private $_rank;

    public function __construct()
    {
        $this->_rank = new RankMethods();
    }

    function getAdminPages(){
        return [
            "membres" => [
                "icon" => "♟",
                "power" => 5
            ], "stats" => [
                "icon" => "⌚",
                "power" => 3
            ], "support" => [
                "icon" => "✎",
                "power" => 2,
                "modification_power" => 5,
                "resolve_ticket_power" => 5,
            ], "forum" => [
                "icon" => "✉",
                "power" => 5
            ], "news" => [
                "icon" => "<i class='fas fa-newspaper'></i>",
                "power" => 5
            ], "bêta" => [
                "icon" => "<i class='fas fa-heartbeat'></i>",
                "power" => 5
            ], "sanctions" => [
                "icon" => "<i class='fas fa-gavel'></i>",
                "power" => 2,
                "ban_power" => 5,
            ], "console" => [
                "icon" => "<i class='fas fa-terminal'></i>",
                "power" => 5
            ]
        ];
    }

    function canAccessPanel($rank){
        return $this->_rank->getRank($rank)['power']>=$this->minPowerPanel;
    }

}