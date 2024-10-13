<?php
declare(strict_types=1);
namespace App\Models;

use App\Database\Database;
use InvalidArgumentException;

Class ClientModel
{
    private $conn;
    public function __construct(private Database $database)
    { 
        $this->conn = $this->database->getConnection();
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

    
}
