<?php
include 'config.php'; // panggil koneksi database

// Ambil data dari form
$email = $_POST['email'];
$password = $_POST['password'];

// Cari data user berdasarkan email
$sql = "SELECT * FROM users WHERE email = '$email'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Kalau user ketemu
    $row = $result->fetch_assoc();

    // Verifikasi password
    if (password_verify($password, $row['password'])) {
        echo "<script>
                alert('Login successful!');
                window.location.href = '../dashboard.php';
              </script>";
    } else {
        echo "<script>
                alert('Incorrect password!');
                window.history.back();
              </script>";
    }
} else {
    echo "<script>
            alert('Email not found!');
            window.history.back();
          </script>";
}

$conn->close();
?>