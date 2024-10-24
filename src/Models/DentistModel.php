<?php

declare(strict_types=1);

namespace App\Models;

use App\Database\Database;
use PDO;
use InvalidArgumentException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\core\secretekey;
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
        return in_array($number, $this->selectSpecialties()['specialty_id']);
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

    public function getGeneralDentistsId():array{
        $sql = "SELECT id FROM dentist WHERE specialty = 1";
        $stmt = $this->conn->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
        return $result?:[];
        }

    public function getNotGeneralDentistId():array{
        $sql = "SELECT id FROM dentist WHERE specialty != 1";
        $stmt = $this->conn->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
        return $result?:[];
    }
}