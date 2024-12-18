<?php
declare(strict_types=1);
namespace App\Helpers\DTOs;

Class ClientDTO{
    public function __construct(
        public readonly ?int $id,
        public readonly ?string $clients_name,
        public readonly ?string $clients_lastName,
        public readonly ?string $clients_email,
        public readonly ?string $clients_password
    ) {  
    }

}