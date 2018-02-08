<?php

namespace App\Model;

use PDO;

class Model {

    protected $db;

    const config = [
        'dbname' => 'grupo++',
        'host' => 'localhost',
        'dbuser' => 'root',
        'dbpass' => ''
    ];

    public function __construct() {
        $option = [PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES UTF8"];
        $this->db = new PDO("mysql:dbname=" . self::config['dbname'] . ";host=" . self::config['host'], self::config['dbuser'], self::config['dbpass'], $option);
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    }

}
