<?php

namespace App\Controllers;
use App\Models\DentistModel;
use App\Helpers\Responser;
use App\Helpers\DTOs\DentistDTO;
use App\Helpers\DTOs\ClientDTO;
use App\Helpers\DTOs\SpecialistDTO;

class DentistController{
public function __construct(private DentistModel $dentistModel){}

public function insertDentist(DentistDTO $data){
$this->dentistModel->insertDentist(
    $data->dentist_name,
    $data->dentist_lastName,
    $data->dentist_email,
    $data->dentist_password,
    $data->specialty
)?
Responser::success(201,"ok"):
Responser::error(400, "error");
}

public function getSpecialties(){
    $specialties = $this->dentistModel->selectSpecialties();
    if($specialties){
        Responser::success(200, $specialties);
    }else{
        Responser::error(401, "error");
    }
    
}

public function getSpecialists(){
    $specialist = $this->dentistModel->getNotGeneralDentists();
    if($specialist){
        Responser::success(200, $specialist);
    }else{
        Responser::error(401, "error");
    }
}

public function insertAvailabilityOfSpecialist(SpecialistDTO $data){
    $this->dentistModel->insertAvalabiltyOfSpecialist(
     $data->id,
     $data->day,
     $data->inAt,
     $data->outAt
    )?
    Responser::success(201, "ok"):
    Responser::error(400, "error");
}

public function getAvalabilty(SpecialistDTO $data){
    $availabality = $this->dentistModel->selectAvalabilityOfSpecialist($data->id);
    if(count($availabality)>0){
        Responser::success(200,$availabality);
    }else{
        Responser::error(400,"There is no fetched data");
    }
}

public function getDaysofTheWeek(int $id){
    $days = $this->dentistModel->getDays($id);
    if(count($days)>0){
        Responser::success(200,$days);
    }else{
        Responser::error(400,"There is no fetched data");
    }
}

public function deleteDentist(int $id){
    $deleted = $this->dentistModel->deleteDentist($id)?
    Responser::success(200, "Dentist Deleted"):
    Responser::error(400, "error");
}

public function getAllDentist(){
    $dentists = $this->dentistModel->getAllDentist();
    if($dentists){
      Responser::success(200, $dentists);
    }else{
        Responser::error(400, "There is no fetched data");
    }
}

}