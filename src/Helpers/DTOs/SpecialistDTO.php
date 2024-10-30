<?php
declare(strict_types=1);
namespace App\Helpers\DTOs;

Class SpecialistDTO{
    public function __construct(
        public readonly ?int $id,
        public readonly ?int $day,
        public readonly ?int $inAt,
        public readonly ?int $outAt
        ){}
}