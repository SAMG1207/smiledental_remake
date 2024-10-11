<?php
require __DIR__ . '/../../vendor/autoload.php';
use App\Database\Database;


$db = new Database();
$conn = $db->getConnection();
try{
    $urlImgs = [
        ['name' => 'carillas', 'url' => 'img/carillas.jpg'],
        ['name' => 'endo', 'url'=>'img/endo.jpg'],
        ['name'=>'equipo', 'url'=>'img/equipo.jpg'],
        ['name'=>'implante', 'url'=>'img/implante.jpg'],
        ['name'=>'sonrisa', 'url'=>'img/sonrisa.jpg']
    ];
$sql = "CREATE TABLE IF NOT EXISTS images(
id INT NOT NULL PRIMARY KEY,
img_name VARCHAR(20) NOT NULL,
img_url VARCHAR (50) NOT NULL)
ENGINE=INNODB";
$conn->exec($sql);
$conn->beginTransaction();
foreach($urlImgs as $index => $value){
    $imgName = $value['name'];
    $imgUrl = $value['url'];
    $indx = $index+1;
    $insert = "INSERT INTO images(id, img_name, img_url) VALUES
    ('$indx', '$imgName', '$imgUrl')";
    $conn->exec($insert);
 }
 $conn->commit();

}catch(PDOException $e){
    $conn->rollBack();
    echo "Error: " . $e->getMessage();
}