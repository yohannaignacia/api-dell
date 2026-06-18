<?php
// Mengatur header agar aplikasi Flutter mengenali format JSON
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

// Memanggil file koneksi database
// Pastikan path ke database.php benar (sesuaikan jika folder config ada di tempat lain)
include_once '../config/database.php';

// Menyiapkan query untuk mengambil data
// Kolom 'fullname' disesuaikan dengan struktur tabel yang kamu kirimkan
$query = "SELECT id, fullname, email, role, status FROM users";

// Eksekusi query menggunakan variabel $conn (pastikan ini nama variabel koneksi di database.php)
$result = $conn->query($query);

// Membuat array untuk menampung hasil data
$users = array();

// Mengecek apakah data ditemukan
if ($result && $result->num_rows > 0) {
    // Mengubah setiap baris data menjadi array asosiatif
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
    // Mengirim data dalam bentuk format JSON
    echo json_encode($users);
} else {
    // Jika data kosong, kirim array kosong
    echo json_encode([]);
}

// Menutup koneksi
$conn->close();
?>