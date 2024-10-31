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
        specialty INT NOT NULL,
        

    )";


    $conn->exec($sql);
};

function createSpecialtyTable(){
    $db = new Database();
    $conn = $db->getConnection();
    $sql = "CREATE TABLE IF NOT EXISTS specialties (
        id_specialty INT PRIMARY KEY,
        specialty_name VARCHAR(20) NOT NULL,
        descripcion MEDIUMTEXT NOT NULL,
        url_image VARCHAR(255) 
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

function createSpecialtyDentist(){
    $db = new Database();
    $conn = $db->getConnection();
    $sql = " CREATE TABLE IF NOT EXISTS specialists(
        id_dentist INT NOT NULL,
        url_image VARCHAR(50),
        FOREIGN KEY (id_dentist) REFERENCES dentist(id)
        );
    ";
    $conn->exec($sql);
}

function createSpecialtyAvailability(){
    $db = new Database();
    $conn = $db->getConnection();
    $sql = " CREATE TABLE IF NOT EXISTS specialists_availabity(
        id_availability INT PRIMARY KEY AUTO_INCREMENT,
        id_dentist INT NOT NULL,
        numbered_day INT NOT NULL,
        in_at INT NOT NULL,
        out_at INT NOT NULL,
        FOREIGN KEY (id_dentist) REFERENCES dentist(id),
        FOREIGN KEY (numbered_day) REFERENCES week(id_day)
        );
    ";
    $conn->exec($sql);
}

function createWeekTable(){
    $db = new Database();
    $conn = $db->getConnection();
    $sql ="CREATE TABLE IF NOT EXISTS week(
    id_day INT PRIMARY KEY AUTO_INCREMENT,
    day_name VARCHAR(10)
    )";
    $conn->exec($sql);
}

function insertDays(){
    $db = new Database();
    $conn = $db->getConnection();
    $days = ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];
    for($i = 0; $i < 7; $i++){
        $sql = "INSERT INTO week (id_day, day_name) VALUES($i, '{$days[$i]}')";
         $conn->exec($sql);
    }
   
}




//createSpecialtyTable();
//insertSpecialties();
//createGeneralTable();
//addForeignKeyToDentist();
//createSpecialtyDentist();

createWeekTable();
insertDays();
//createSpecialtyAvailability();