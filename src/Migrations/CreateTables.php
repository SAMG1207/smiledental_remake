<?php

require __DIR__ . '/../../vendor/autoload.php'; // FROM THE COMMAND CONSOLE
use App\Database\Database;


function createMainTables(){
$db = new Database();
$conn = $db->getConnection();
$tables = ['clients' , 'dentist' , 'adm'];
   foreach($tables as $table){
    $sql = "CREATE TABLE IF NOT EXISTS ".$table."
    (id INT AUTO_INCREMENT PRIMARY KEY,
    ".$table."_name VARCHAR(50),
    ".$table."_lastName VARCHAR(50),
    ".$table."_email VARCHAR(255),
    ".$table."_password VARCHAR(255)
    )";
    $conn->exec($sql);
   }
}

createMainTables();


