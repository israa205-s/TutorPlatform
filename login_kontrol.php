<?php
// 1. Hataları ekrana basması için bu satırları en başa ekliyoruz
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // SHA-256 şifreleme
    $hashed_password = hash('sha256', $password);

    try {
        // Sorguyu hazırlıyoruz
        $sorgu = $db->prepare("SELECT * FROM users WHERE email = :email AND password = :password");
        $sorgu->bindParam(':email', $email);
        $sorgu->bindParam(':password', $hashed_password);
        $sorgu->execute();

        $user = $sorgu->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Giriş başarılı
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['role'] = $user['role_type'];

            echo "<script>window.location.href='dashboard.php';</script>";
            exit();
        } else {
            // Giriş başarısız olduğunda burası çalışmalı
            echo "Girdiğiniz e-posta: " . htmlspecialchars($email) . "<br>";
            echo "Girdiğiniz şifrenin hash hali: " . $hashed_password . "<br>";
            echo "<script>alert('Hatalı e-posta veya şifre!'); window.location.href='index.php';</script>";
            exit();
        }
    } catch (PDOException $e) {
        // Eğer veritabanı aşamasında bir hata varsa burada göreceğiz
        die("Veritabanı Hatası: " . $e->getMessage());
    }
} else {
    echo "Form gönderilmedi!";
}
?>