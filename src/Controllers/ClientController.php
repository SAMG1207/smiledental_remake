<?php
namespace App\Controllers;
use App\Models\ClientModel;
use App\Helpers\Responser;
use App\Helpers\DTOs\ClientDTO;

Class ClientController
{
    
    public function __construct(private ClientModel $clientModel)
    {
    }

    public function insertClient(ClientDTO $DTO)
    {
        $this->clientModel->createClient(
                  
            $DTO->clients_name,
            $DTO->clients_lastName,
            $DTO->clients_email,
            $DTO->clients_password
        )?
        Responser::success(201, "ok"):
        Responser::error(400, 'error');
    }

    public function login(ClientDTO $DTO){
      $jwt =  $this->clientModel->loginClient(
            $DTO->clients_email,
            $DTO->clients_password
      );
      if($jwt){
        Responser::success(200, ['token'=>$jwt, 'redirect'=>'/dashboard']);
      
      }else{
        Responser::error(401,'error');
      }
    }

      public function getInfoFromClient(clientDTO $DTO){
        $data = $this->clientModel->getInfoFromClient($DTO->id);
        if(!empty($data)){
          Responser::success(200, $data);
        }else{
          Responser::error(400, "error");
        }
      }
}
