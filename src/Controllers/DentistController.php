<?php

namespace App\Controllers;
use App\Models\DentistModel;
use App\Helpers\Responser;
use App\Helpers\DTOs\DentistDTO;

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
}