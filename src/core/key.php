<?php
namespace App\core\secreteKey;
require_once './vendor/autoload.php';
use Dotenv\Dotenv;
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();
$secretKey = $_ENV['SECRET_KEY'];