<?php
declare(strict_types=1);
namespace App\Models;

use App\Database\Database;
use InvalidArgumentException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Dotenv\Dotenv; 
use PDO;

Class ClientModel
{
    private $conn;
    private $privateKey;
    public function __construct(private Database $database)
    { 
        $this->conn = $this->database->getConnection();
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../..');
        $dotenv->load();
        $this->privateKey = $_ENV['SECRET_KEY'];
    }
    
    public function isRegistered(string $email):bool
    {
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }
        $sql ="SELECT * FROM clients WHERE clients_email = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(1, $email);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    public function getId(string $email):int{
        if(!filter_var($email, FILTER_VALIDATE_EMAIL) || $this->isRegistered($email)) {
            return 0;
        }
        $sql = "SELECT id FROM clients WHERE clients_email = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(1, $email);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result;
    }

    public function createClient(string $name, string $lastname, string $email, string $password):bool
    {
        if(!filter_var($email, FILTER_VALIDATE_EMAIL) || $this->isRegistered($email)) {
            return false;
        }
        $passwordToInsert = password_hash($password, PASSWORD_DEFAULT);
        if(!DentistModel::testString($name)||!DentistModel::testString($lastname)){
            throw new InvalidArgumentException('please check both name and lastname, only letters are admited');
        }
        $sql = "INSERT INTO clients(clients_name, clients_lastName, clients_email, clients_password) VALUES (?,?,?,?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(1, $name);
        $stmt->bindParam(2, $lastname);
        $stmt->bindParam(3, $email);
        $stmt->bindParam(4, $passwordToInsert);
        return $stmt->execute();
    }

    public function loginClient(string $email, string $password):bool|string{
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
            return false;
        }

        $sql ="SELECT * FROM clients WHERE clients_email = ?";
        $stmt= $this->conn->prepare($sql);
        $stmt->bindParam(1, $email);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if($user && password_verify($password, $user['clients_password'])){
            $payload = [
                "id" => $user['id'],
                "email" => $user['clients_email'],  
                "exp" => time() + 3600      // Expira en 1 hora
            ];
           
            $jwt = JWT::encode($payload, $this->privateKey, 'HS256');

            return $jwt;
        }
        return false;
    }

    public function getInfoFromClient(int $id):array{
        $sql = "SELECT id, clients_name, clients_lastName, clients_email FROM clients WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if($result){
            return $result;
        }
        return[];
    }
        
}
