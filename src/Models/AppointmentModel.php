<?php
declare(strict_types=1);
namespace App\Models;

use App\Database\Database;
use App\Models\ClientModel;
use App\Models\DentistModel;
use Exception;
use PDO;
class AppointmentModel{
    private $conn;
    private array $timeOpen;
    private int $entryTime = 8;
    private int $closingTime = 21;
    public function __construct(private ClientModel $clientModel, private Database $database, private DentistModel $dentistModel){
        $this->conn = $this->database->getConnection();
        for ($i = 0, $time = $this->entryTime; $time < $this->closingTime; $i++, $time++) {
            $this->timeOpen[$i] = $time;
        }
    }

    private function validateAppointmentTime(int $time):bool{
        return $time >=8 && $time <= 20;
    }

    public function insertAppointment(string $email, int $id_dentist, string $date, int $time):bool{
        if(!$this->validateAppointmentTime($time)){
            throw new \InvalidArgumentException("time of appointment must be between 8 and 20");
        };
        $clientId = $this->clientModel->getId($email);
        $sql = "INSERT INTO appointment(id_client, id_dentist, app_date, app_time)VALUES(?,?,?,?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(1, $clientId);
        $stmt->bindParam(2, $id_dentist);
        $stmt->bindParam(3,$date);
        $stmt->bindParam(4, $time);
        return $stmt->execute();
    }

    private function getAppointmentsByDateOfGeneralDentists(string $date):array{
        // THIS IS TO GET THE OCCUPIED HOURS
        $ids_general = $this->dentistModel->getGeneralDentistsId();
        if (empty($ids_general)) {
            return [];
        }
        
        $sql ="SELECT app_time FROM appointment WHERE app_date = ? AND id_dentist = ?";
        $occupiedHours = [];
        foreach($ids_general as $id_general){
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(1, $date);
            $stmt->bindParam(2, $id_general);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_COLUMN,0);
            $occupiedHours = array_merge($occupiedHours, $result);
        }
          return $occupiedHours ?: [];
    }

    public function getAvailableHoursByDate (string $date):array{
      $occupiedHours = $this->getAppointmentsByDateOfGeneralDentists($date);
      $availableHours = array_diff($this->timeOpen, $occupiedHours);
      sort($availableHours);
      return $availableHours;
    }

    private function getOccupiedDentistsIdByDayAndTime(string $date, int $time):array{
        $sql ="SELECT id_dentist FROM appointment WHERE app_date = ? AND app_time = ?";
        $stmt= $this->conn->prepare($sql);
        $stmt->bindParam(1, $date);
        $stmt->bindParam(2, $time);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_COLUMN);
        return $result?:[];
    }
    
    public function getAvailableGeneralDentistByDayAndTime(string $date, int $time):array{
        $idGeneralDentists = $this->dentistModel->getGeneralDentistsId();
        $occupiedDentists = $this->getOccupiedDentistsIdByDayAndTime($date, $time);
        $availableDentists = array_diff($idGeneralDentists, $occupiedDentists);
        return $availableDentists;
    }

    public function assignAppointmentToGeneralDentist(string $date, int $time, $email):bool{
      $availableDentists = $this->getAvailableGeneralDentistByDayAndTime($date, $time);
      if(empty($availableDentists)){
        throw new Exception("there is no dentist available for the selected date and time");
      }
      $randomKey = array_rand($availableDentists);
      $idClient = $this->clientModel->getId($email);
      if(!filter_var($idClient, FILTER_VALIDATE_INT) || $idClient===0){
        throw new Exception("this id is not correct, please check the client`s email");
      }
      $sql = "INSERT INTO appointment (id_client, id_dentist, app_date, app_time) VALUES(?,?,?,?)";
      $stmt=$this->conn->prepare($sql);
      $stmt->bindParam(1, $idClient);
      $stmt->bindParam(2, $availableDentists[$randomKey]);
      $stmt->bindParam(3, $date);
      $stmt->bindParam(4, $time);
      return $stmt->execute();
    }
}