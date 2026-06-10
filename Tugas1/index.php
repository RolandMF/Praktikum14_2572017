<?php
// 2572017 - Roland Michael Febrian
include_once "koneksi.php"; 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Praktikum 14 - 2572017</title>
</head>
<body>
    <h1>halaman pertama</h1>
    
    <?php
    echo "Ini dari PHP.";
    $nama = "Roland";
    echo "<p>Hello, ".$nama.".</p>"; 
    ?>

    <fieldset> 
        <legend> Isian Data </legend>
        <form action="proses.php" method="get">
            <input type="text" name="fname" placeholder="First Name" required>
            <input type="email" name="gmail" placeholder="Gmail" required>
            <input type="submit" name="btnSubmit" value="Simpan">
        </form>
    </fieldset>

    <?php
    // Menampilkan notifikasi sukses/gagal kalau habis simpan data
    $msg = isset($_GET['msg']) ? trim($_GET['msg']) : "";
    if ($msg != "") {
        echo "<p style='color:green; font-weight:bold;'>".htmlspecialchars($msg)."</p>";
    }
    ?>

    <br>
    <?php $keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : ''; ?>
    <fieldset>
        <legend>Cari Pengguna</legend>
        <form method="get" action="index.php">
            <input type="text" name="keyword" placeholder="Masukkan nama..." value="<?= htmlspecialchars($keyword) ?>">
            <button type="submit">Cari</button>
            <?php if ($keyword != ''): ?>
                <a href="index.php"><button type="button">Reset</button></a>
            <?php endif; ?>
        </form>
    </fieldset>
    <br>

    <?php
    // =========================================================================
    // BAGIAN TAMPIL DATA / SELECT 
    // =========================================================================
    try {
        // Query SELECT berdasarkan keyword pencarian (Fitur Searching)
        if ($keyword != '') {
            $sqlSelect = "SELECT user_id, first_name, email FROM pengguna WHERE first_name LIKE :keyword";
            $stmtSelect = $conn->prepare($sqlSelect);
            $stmtSelect->bindValue(':keyword', "%$keyword%", PDO::PARAM_STR);
        } else {
            $sqlSelect = "SELECT user_id, first_name, email FROM pengguna";
            $stmtSelect = $conn->prepare($sqlSelect);
        }
        
        $stmtSelect->execute();
        
        // Menampilkan data ke tabel HTML
        if ($stmtSelect->rowCount() > 0) {
            echo "<table border='1' cellpadding='5' cellspacing='0'>";
            echo "<tr><th>ID</th><th>Firstname</th><th>Email</th></tr>";
            
            while ($row = $stmtSelect->fetch(PDO::FETCH_ASSOC)) {
                echo "<tr>";
                echo "<td>". $row['user_id']. "</td>"; 
                echo "<td>". $row['first_name']. "</td>";
                echo "<td>". $row['email']. "</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p>No records found.</p>";
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
    
    // Menutup koneksi database
    $conn = null;
    ?>
  
</body>
</html>