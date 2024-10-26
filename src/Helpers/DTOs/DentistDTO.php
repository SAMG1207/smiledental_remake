<?php
declare(strict_types=1);
namespace App\Helpers\DTOs;

Class DentistDTO{
    public function __construct(
        
        public readonly string $dentist_name,
        public readonly string $dentist_lastName,
        public readonly string $dentist_email,
        public readonly string $dentist_password,
        public readonly int $specialty){}
}