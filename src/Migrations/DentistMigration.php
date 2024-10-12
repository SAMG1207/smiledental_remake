<?php

require __DIR__ . '/../../vendor/autoload.php'; // FROM THE COMMAND CONSOLE
use App\Database\Database;

function createGeneralTable(){
    $db = new Database();
    $conn = $db->getConnection();

    
    $sql = "CREATE TABLE IF NOT EXISTS dentist (
        id INT AUTO_INCREMENT PRIMARY KEY,
        dentist_name VARCHAR(50) NOT NULL,
        dentist_lastName VARCHAR(50) NOT NULL,
        dentist_email VARCHAR(255) NOT NULL,
        dentist_password VARCHAR(255) NOT NULL,
        specialty INT NOT NULL
    )";


    $conn->exec($sql);
};

function createSpecialtyTable(){
    $db = new Database();
    $conn = $db->getConnection();
    $sql = "CREATE TABLE IF NOT EXISTS specialties (
        id_specialty INT PRIMARY KEY,
        specialty_name VARCHAR(20) NOT NULL
    )";
    $conn->exec($sql);
};

function insertSpecialties(){
    $db = new Database();
    $conn = $db->getConnection();
    $specialties = ['general', 'endodontics', 'surgery', 'orthodontics'];
    foreach ($specialties as $index => $specialty) {
        $number = $index + 1;
        $sql = "INSERT INTO specialties (id_specialty, specialty_name) VALUES ($number, '$specialty')";
        $conn->exec($sql);
    };
};

function addForeignKeyToDentist() {
    $db = new Database();
    $conn = $db->getConnection();
    $sql = "
        ALTER TABLE dentist 
        ADD CONSTRAINT fk_specialty FOREIGN KEY (specialty) 
        REFERENCES specialties(id_specialty) 
        ON DELETE CASCADE ON UPDATE CASCADE
    ";
    $conn->exec($sql);
};

//createSpecialtyTable();
//insertSpecialties();
//createGeneralTable();
addForeignKeyToDentist();