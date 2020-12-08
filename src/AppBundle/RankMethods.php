<?php

namespace App\AppBundle;

class RankMethods {

    function getRanksName($powerLimit = -1){
        $names = [];
        foreach($this->getRanks() as $name=>$values){
            if($powerLimit !== -1 && $values['power']>=$powerLimit) continue;
            $names[] = $name;
        }

        return $names;
    }

    function getRanks(){

        return [
            "ILYRIEN" => [
                'power' => 0,
                'color' => 'orange',
            ],
            "VIP" => [
                'power' => 0,
                'color' => '#46FFF6',
            ],
            "ROYAL" => [
                'power' => 0,
                'color' => "#FFEE16",
            ],
            "EPIC" => [
                'power' => 0,
                'color' => "#A32499",
            ],
            "LEGENDE" => [
                'power' => 0,
                'color' => "#F527E6",
            ],
            "ANIMATEUR" => [
                'power' => 1,
                'color' => "#1FAF17",
            ],
            "HELPEUR" => [
                'power' => 2,
                'color' => "#0CC1B8",
            ],
            "MODO" => [
                'power' => 3,
                'color' => "#32E818",
            ],
            "BUILDEUR" => [
                'power' => 1,
                'color' => "#0B2BC5",
            ],
            "ADMIN" => [
                'power' => 5,
                'color' => "red",
            ],
            "RESPSTAFF" => [
                'power' => 6,
                'color' => "#2481CF",
            ],
            "FONDATEUR" => [
                'power' => 7,
                'color' => "#C90D0D",
            ],
        ];
    }

    function getRank($rank){
        if(isset($this->getRanks()[$rank])) return $this->getRanks()[$rank];
        return $this->getRanks()["ILYRIEN"];
    }
}