<?php

namespace App\AppBundle;

use Doctrine\DBAL\Exception\TableNotFoundException;
use function App\Controller\convert_to_utf8_recursively;

class ServerMethods {

    private $path;

    public function __construct()
    {
        $this->path = (new ServerInformations(null))->getPath();
    }

    function getOnlinePlayers($em_server){
        $RAW_QUERY = 'SELECT id, pseudo, groupe, server FROM players where server != "NONE"';
        $data = [];

        if(!empty($_POST)) {
            if(!empty($_POST['search'])) {
                $value = htmlspecialchars($_POST['search']);
                $RAW_QUERY.= " AND pseudo LIKE ?";
                $data[] = "%$value%";
            }
        }

        try {
            $statement = $em_server->getConnection()->prepare($RAW_QUERY);
            $statement->execute($data);
            $results = $statement->fetchAll();
        }catch (TableNotFoundException $e){}

        return $results;
    }

    function convert_to_utf8_recursively($dat){
        if( is_string($dat) ){
            return mb_convert_encoding($dat, 'UTF-8', 'UTF-8');
        }
        if( is_array($dat) ){
            $ret = [];
            foreach($dat as $i => $d){
                $ret[$i] = $this->convert_to_utf8_recursively($d);
            }
            return $ret;
        }
        return $dat;
    }

    function getServers($online = false, $rcon = false){
        $folders = scandir($this->path);
        $servers = [];

        foreach($folders as $folder) {
            $server_info = new ServerInformations($folder);
            if($server_info->isServer()){
                if($online) {
                    if($server_info->isOnline()) {
                        if($rcon) {
                            if($server_info->getProperty("enable-rcon") === "true") $servers[] = $folder;
                        }else $servers[] = $folder;
                    }
                }else $servers[] = $folder;
            }
        }

        return $servers;
    }

}