<?php

declare(strict_types=1);

namespace App\Helpers\DTOs;

class AppointmentDTO{
    public function __construct(
        public readonly ?string $date,
        public readonly ?int $time,
        public readonly ?string $email
        ){}
}