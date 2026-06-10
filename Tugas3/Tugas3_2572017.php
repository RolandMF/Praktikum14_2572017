<?php
// 2572017 - Roland Michael Febrian
include 'koneksi.php';

$msg = "";
$msgClass = "";

// Saat form disubmit (Metode POST)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = trim($_POST['nama']);
    $asal = trim($_POST['asal']);
    $komentar = trim($_POST['komentar']);

    // Validasi PHP: cek apakah ada field kosong
    if (empty($nama) || empty($asal) || empty($komentar)) {
        $msg = "Semua field harus diisi!";
        $msgClass = "alert-danger";
    } else {
        // Jika valid: simpan ke database menggunakan PDO
        try {
            $sql = "INSERT INTO buku_tamu (nama, asal, komentar) VALUES (:nama, :asal, :komentar)";
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                ':nama' => $nama,
                ':asal' => $asal,
                ':komentar' => $komentar
            ]);
            $msg = "Komentar berhasil ditambahkan!";
            $msgClass = "alert-success";
        } catch (PDOException $e) {
            $msg = "Error saat menyimpan: " . $e->getMessage();
            $msgClass = "alert-danger";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buku Tamu - 2572017</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body class="bg-light pb-5">

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h3 class="card-title mb-4">Buku Tamu</h3>
                    
                    <?php if ($msg != ""): ?>
                        <div class="alert <?= $msgClass ?>"><?= $msg ?></div>
                    <?php endif; ?>

                    <form method="POST" action="">
                        <div class="mb-3">
                            <label class="form-label">Nama</label>
                            <input type="text" name="nama" class="form-control" placeholder="Masukkan nama kamu">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Asal Kota</label>
                            <input type="text" name="asal" class="form-control" placeholder="Contoh: Bandung">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Komentar</label>
                            <textarea name="komentar" class="form-control" rows="3" placeholder="Tulis pesan kamu di sini..."></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Kirim Komentar</button>
                    </form>
                </div>
            </div>

            <h4 class="mb-3">Daftar Komentar</h4>
            <?php
            try {
                // Tampilkan semua komentar ORDER BY waktu DESC
                $sql_select = "SELECT * FROM buku_tamu ORDER BY waktu DESC";
                $stmt_select = $conn->prepare($sql_select);
                $stmt_select->execute();
                
                // Tampilkan total jumlah komentar
                $total = $stmt_select->rowCount();
                echo "<p class='text-muted fw-bold'>Total Komentar: " . $total . "</p>";

                if ($total > 0) {
                    echo "<div class='list-group shadow-sm'>";
                    // Gunakan while untuk menampilkan tiap komentar
                    while ($row = $stmt_select->fetch(PDO::FETCH_ASSOC)) {
                        echo "<div class='list-group-item py-3'>";
                        // Gunakan htmlspecialchars saat menampilkan data
                        echo "<h6 class='mb-1 fw-bold'>" . htmlspecialchars($row['nama']) . " <span class='text-muted fw-normal fs-6'>- " . htmlspecialchars($row['asal']) . "</span></h6>";
                        echo "<small class='text-muted d-block mb-2'>" . htmlspecialchars($row['waktu']) . "</small>";
                        echo "<p class='mb-0'>\"" . nl2br(htmlspecialchars($row['komentar'])) . "\"</p>";
                        echo "</div>";
                    }
                    echo "</div>";
                } else {
                    // Jika belum ada komentar
                    echo "<div class='alert alert-info'>Belum ada komentar</div>";
                }
            } catch (PDOException $e) {
                echo "<div class='alert alert-danger'>Gagal memuat komentar: " . $e->getMessage() . "</div>";
            }
            
            // Menutup koneksi database di akhir
            $conn = null;
            ?>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>