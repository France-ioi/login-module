<?php
namespace App\LoginModule\LTI;

use App\LoginModule\LTI\Tool\LTI_Data_Connector;
use PDO;

class LTIPDO {

    public function __construct() {
        $this->connected = false;
    }


    public function db() {
        if(!$this->connected) {
            $this->db = $this->connectDB();
        }
        return $this->db;
    }


    public function connector() {
        return LTI_Data_Connector::getDataConnector($this->db(), 'PDO');
    }


    private function connectDB() {
        $now = new \DateTime();
        $mins = $now->getOffset() / 60;
        $sgn = ($mins < 0 ? -1 : 1);
        $mins = abs($mins);
        $hrs = floor($mins / 60);
        $mins -= $hrs * 60;
        $offset = sprintf('%+d:%02d', $hrs*$sgn, $mins);
        $conf = config('lti.db');
        try {
            $pdo_options = [];
            $pdo_options[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;
            $pdo_options[PDO::MYSQL_ATTR_INIT_COMMAND] = "SET NAMES utf8";
            $connexionString = "mysql:host=".$conf['host'].";dbname=".$conf['database'].";charset=utf8";
            $db = new PDO($connexionString, $conf['user'], $conf['password'], $pdo_options);
            $db->exec("SET time_zone='".$offset."';");
        } catch (PDOException $e) {
            die("Erreur : " . $e->getMessage());
        }
        $this->connected = true;
        return $db;
    }
}
