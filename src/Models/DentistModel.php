<?php

declare(strict_types=1);

namespace App\Models;

use App\Database\Database;
use PDO;
use InvalidArgumentException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\core\secretekey;
use Exception;

Class DentistModel{

    private $conn;
    public function __construct(private Database $database){
        $this->conn = $this->database->getConnection();}

    public static function testString(string $string):bool{
            $regex = '/^[A-Za-zÀ-ÿ]+(?: [A-Za-zÀ-ÿ]+)*$/';
            return preg_match($regex, $string) === 1;
    }

    public function selectSpecialties():array{
       $sql = "SELECT * FROM specialties";
       $stmt = $this->conn->query($sql);
       $result = $stmt->fetchAll();
       return $result;
    }

    
    private function isAnSpeciality(int $number):bool{
        $sql = "SELECT id_specialty FROM specialties";
        $stmt = $this->conn->query($sql);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_COLUMN, 0); // Extrae solo la primera columna como un array
        return in_array($number, $result);
    }


    public function insertDentist(
          string $dentist_name,
          string $dentist_lastName,
          string $dentist_email, 
          string $dentist_password, 
          int $specialty
          ):bool{
        if(!$this->isAnSpeciality($specialty)){
            throw new InvalidArgumentException('this specialty is not registered yet, so it can`t be used');
        }
        if(!$this->testString($dentist_name) || !$this->testString($dentist_lastName)){
            throw new InvalidArgumentException('please check both name and lastname, only letters are admited');
        }
        if(!filter_var($dentist_email, FILTER_VALIDATE_EMAIL)){
            throw new InvalidArgumentException('no valid email');
        }
        if($this->isRegistered($dentist_email)){
            throw new InvalidArgumentException(' dentist already registered');
        }
        $hashedPassword = password_hash($dentist_password, PASSWORD_DEFAULT);
        $sql ="INSERT INTO dentist (dentist_name, dentist_lastName, dentist_email, dentist_password, specialty) VALUES (?,?,?,?,?)";
        $stmt=$this->conn->prepare($sql);
        $stmt->bindParam(1, $dentist_name);
        $stmt->bindParam(2, $dentist_lastName);
        $stmt->bindParam(3, $dentist_email);
        $stmt->bindParam(4, $hashedPassword);
        $stmt->bindParam(5, $specialty);
        return $stmt->execute();
    }

    public function deleteDentist(int $id):bool{
        $sql = "DELETE FROM dentist WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(1,$id);
        $stmt->execute();
        return true;
    }

    private function isRegistered($email):bool{
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
            throw new InvalidArgumentException('no valid email');
        }
        $sql ="SELECT * FROM dentist WHERE dentist_email = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(1, $email);
        $stmt->execute();
        return $stmt->fetch() !== false;
    }

    public function getGeneralDentistsId():array{
        $sql = "SELECT id FROM dentist WHERE specialty = 1";
        $stmt = $this->conn->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
        return $result?:[];
        }

    public function getNotGeneralDentists():array{
        $sql = "SELECT d.id, d.dentist_name, d.dentist_lastName, s.specialty_name
        FROM dentist d
        INNER JOIN specialties s ON d.specialty = s.id_specialty
        WHERE s.id_specialty != 1"; 
       $stmt = $this->conn->query($sql);
       $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
       return $result ?: [];
    }

    public function insertAvalabiltyOfSpecialist(int $id, int $day, int $inAt, int $outAt):bool{
        $sql = "INSERT INTO specialists_availabity (id_dentist, numbered_day, in_at, out_at) VALUES(?,?,?,?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(1, $id);
        $stmt->bindParam(2, $day);
        $stmt->bindParam(3, $inAt);
        $stmt->bindParam(4, $outAt);
        return $stmt->execute();
    }

    public function selectAvalabilityOfSpecialist(int $id):array{
       $sql = "SELECT * FROM specialists_availabity WHERE id_dentist = ?";
       $stmt=$this->conn->prepare($sql);
       $stmt->bindParam(1, $id);
       $stmt->execute();
       $result = $stmt->fetchAll();
       return $result;
    }

    public function getDays(int $id):array{
        $sql = "SELECT id_day, day_name 
        FROM week
        WHERE id_day NOT IN (0, 6) 
        AND id_day NOT IN (
            SELECT numbered_day 
            FROM specialists_availabity 
            WHERE id_dentist = ?
        )";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        $result = $stmt->fetchAll();
        return $result;
    }

    public function getAllDentist(): array {
        $sql = "SELECT d.id, d.dentist_name, d.dentist_lastName, s.specialty_name 
                FROM dentist d
                INNER JOIN specialties s ON s.id_specialty = d.specialty";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll();
        return $result;
    }
    
}