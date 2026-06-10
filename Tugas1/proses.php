<?php
include_once "koneksi.php"; 


$firstname = filter_input(INPUT_GET, 'fname');
$email = filter_input(INPUT_GET, 'gmail'); 
$btnSubmit = filter_input(INPUT_GET, 'btnSubmit');

if ($btnSubmit) {
    try {
       
        $sql = "INSERT INTO pengguna (first_name, email) VALUES (:fname, :email)";
        $stmt = $conn->prepare($sql);
        
        
        $stmt->execute([
            ':fname' => $firstname,
            ':email' => $email
        ]);
        
        $msg = "New record created successfully";
    } catch (PDOException $e) {
        $msg = "Error: " . $e->getMessage();
    }
    
  
    $conn = null;
    
  
    header("Location: index.php?msg=" . urlencode($msg));
    exit();
}
?>