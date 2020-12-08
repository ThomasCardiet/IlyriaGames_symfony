<?php

namespace App\AppBundle;

use Doctrine\DBAL\Exception\TableNotFoundException;

class ServerInformations {

    private $path;
    private $server;

    public function __construct($server)
    {
        $this->server = $server;
        $this->path = "C:\Users\admin\Desktop\projet serveur";
    }

    function getPath(){
        return $this->path;
    }

    function getProperty($type){
        $property = null;
        $parameters = [];
        try {
            $property = file_get_contents($this->path . '\\' . $this->server . '\server.properties');
        } catch (\Exception $e) {
            $this->error = [
                'state' => false,
                'msg' => 'Ce serveur n\'existe pas'
            ];
            return $this;
        }

        $templates = explode("\r\n", $property);
        foreach($templates as $template) {
            if(!strpos($template, "=")) continue;
            $params = explode("=", $template);
            $parameters[$params[0]] = $params[1];
        }

        if(isset($parameters[$type])) return $parameters[$type];
        return null;
    }

    function getLogs(){
        $logs = "";
        try {
            $logs = file_get_contents($this->path . '\\' . $this->server . '\logs\latest.log');
        } catch (\Exception $e) {}

        return $this->redefineLogColors($logs);
    }

    function isOnline(){
        $state = @fsockopen($this->getProperty("server-ip"), (int)$this->getProperty("server-port"), $errno, $errstr, 0);
        if($state) return true;
        return false;
    }

    function isServer(){
        try {
            file_get_contents($this->path . '\\' . $this->server . '\server.properties');
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    function redefineLogColors($logs){

        //[0;
        $logs = preg_replace("/\\[0;30;22m/", "<span style='color: #000000;'>", $logs); //0
        $logs = preg_replace("/\\[0;34;22m/", "<span style='color: #0000AA;'>", $logs); //1
        $logs = preg_replace("/\\[0;32;22m/", "<span style='color: #00AA00;'>", $logs); //2
        $logs = preg_replace("/\\[0;36;22m/", "<span style='color: #00AAAA;'>", $logs); //3
        $logs = preg_replace("/\\[0;31;22m/", "<span style='color: #AA0000;'>", $logs); //4
        $logs = preg_replace("/\\[0;35;22m/", "<span style='color: #AA00AA;'>", $logs); //5
        $logs = preg_replace("/\\[0;33;22m/", "<span style='color: #FFAA00;'>", $logs); //6
        $logs = preg_replace("/\\[0;37;22m/", "<span style='color: #AAAAAA;'>", $logs); //7
        $logs = preg_replace("/\\[0;30;1m/", "<span style='color: #555555;'>", $logs);  //8
        $logs = preg_replace("/\\[0;34;1m/", "<span style='color: #5555FF;'>", $logs);  //9

        $logs = preg_replace("/\\[0;32;1m/", "<span style='color: #55FF55;'>", $logs);  //a
        $logs = preg_replace("/\\[0;36;1m/", "<span style='color: #55FFFF;'>", $logs);  //b
        $logs = preg_replace("/\\[0;31;1m/", "<span style='color: #FF5555;'>", $logs);  //c
        $logs = preg_replace("/\\[0;35;1m/", "<span style='color: #FF55FF;'>", $logs);  //d
        $logs = preg_replace("/\\[0;33;1m/", "<span style='color: #FFFF55;'>", $logs);  //e
        $logs = preg_replace("/\\[0;37;1m/", "<span style='color: #FFFFFF;'>", $logs);  //f

        $logs = preg_replace("/\\[5m/", "", $logs);
        $logs = preg_replace("/\\[4m/", "", $logs);
        $logs = preg_replace("/\\[9m/", "", $logs);
        $logs = preg_replace("/\\[21m/", "", $logs);

        $logs = preg_replace("/\\[m/", "</span>", $logs);

        return $logs;
    }

}