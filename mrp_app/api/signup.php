<?php
include 'config.php';

// ambil data dari form
$name = $_POST['name'];
$email = $_POST['email'];
$password = $_POST['password'];

// hash password sebelum simpan
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// simpan ke tabel users
$sql = "INSERT INTO users (name, email, password) VALUES ('$name', '$email', '$hashed_password')";

if ($conn->query($sql) === TRUE) {
    echo "<script>
            alert('Account created successfully!');
            window.location.href = '../login.html';
          </script>";
} else {
    echo "<script>
            alert('Error creating account: " . $conn->error . "');
            window.history.back();
          </script>";
}

$conn->close();
?>