<?php

namespace App\Database;
use Dotenv\Dotenv;
use PDO;
use PDOException;

Class Database{
    private $host;
    private $dbName;
    private $username;
    private $password = "";
    public $conn = null;

    public function __construct(){
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../..');
        $dotenv->load(); 
        $this->host = $_ENV['HOST'];
        $this->dbName = $_ENV['DATABASE'];
        $this->username = $_ENV['USER'];
        if (!isset($_ENV['HOST']) || !isset($_ENV['DATABASE']) || !isset($_ENV['USER'])) {
            die('Error: Some enviroment variables are not set yet.');
        }
    }

    public function getConnection(): ?PDO{
        $this->conn = null;
        try{
           $this->conn = new PDO("mysql:host=".$this->host. ";dbname=".$this->dbName, $this->username, $this->password);
           $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);  // Modo de error
           $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
           $this->conn->exec("set names utf8");
        }catch(PDOException $e){
            echo "error: ".$e->getMessage();
        }
        return $this->conn;
    }

    public function closeConnection(){
         $this->conn=null;
    }
}