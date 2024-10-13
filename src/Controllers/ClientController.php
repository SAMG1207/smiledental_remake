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
}
