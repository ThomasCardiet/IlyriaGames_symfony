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
        $log = "";
        try {
            $log = file_get_contents($this->path . '\\' . $this->server . '\logs\latest.log');
        } catch (\Exception $e) {}

        return $log;
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

}