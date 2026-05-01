<?php

$conn = "";
 
try {
    $servername = "localhost:3306";
    $dbname = "villa_agency";
    $username = "root";
    $password = "";
 
    $conn = new PDO(
        "mysql:host=$servername; dbname=villa_agency",
        $username, $password
    );
    
   $conn->setAttribute(PDO::ATTR_ERRMODE,
                    PDO::ERRMODE_EXCEPTION);
}
catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}

?>