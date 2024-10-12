<?php

declare(strict_types=1);

namespace App\Models;

use App\Database\Database;
use PDO;

Class DentistModel{

    private $conn;
    public function __construct(private Database $database){
        $this->conn = $this->database->getConnection();}

    public function selectSpeacialties():array{
       $sql = "SELECT id_specialty FROM specialties";
       $stmt = $this->conn->query($sql);
       $result = $stmt->fetchAll(PDO::FETCH_COLUMN, 0); // RETURNS ONLY THE NUMBER
       return $result;
    }

    private function isAnSpeciality(int $number):bool{
        return in_array($number, $this->selectSpeacialties());
    }

    public function insertDentist(
          string $dentist_name,
          string $dentist_lastName,
          string $dentist_email, 
          string $dentist_password, 
          int $specialty
          ):bool{
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
}