<?php

require __DIR__ . '/../../vendor/autoload.php'; // FROM THE COMMAND CONSOLE
use App\Database\Database;

function createTableApp(){
    $db = new Database();
    $conn = $db->getConnection();
    $sql ="CREATE TABLE IF NOT EXISTS appointment(
    id_appointment INT AUTO_INCREMENT PRIMARY KEY,
    id_client INT NOT NULL,
    id_dentist INT NOT NULL,
    app_date DATE,
    app_time INT NOT NULL,
    FOREIGN KEY (id_client) REFERENCES clients(id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (id_dentist) REFERENCES dentist(id) ON DELETE CASCADE ON UPDATE CASCADE
    )";

    $conn->exec($sql);
}

function createTableAvailibity(){
    $db = new Database();
    $conn = $db->getConnection();
    $sql = "CREATE TABLE IF NOT EXISTS availability(
    id_avail INT AUTO_INCREMENT PRIMARY KEY,
    id_dentist INT NOT NULL,
    day_dentist INT NOT NULL,
    entryAt INT NOT NULL,
    outAt INT NOT NULL,
    FOREIGN KEY (id_dentist) REFERENCES dentist(id) ON DELETE CASCADE ON UPDATE CASCADE
    )";

    $conn->exec($sql);
}

createTableApp();
createTableAvailibity();