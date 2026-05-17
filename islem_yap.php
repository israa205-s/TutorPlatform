<?php
session_start();
include 'db.php';

$user_id = $_SESSION['user_id'];
$action = $_GET['action'];

if ($action == 'reserve') {
    $session_id = $_GET['session_id'];

    try {
        // 1. Öğrencinin student_id'sini alalım
        $st_query = $db->prepare("SELECT student_id FROM student WHERE user_id = ?");
        $st_query->execute([$user_id]);
        $student = $st_query->fetch();

        // 2. Booking tablosuna ekle (INSERT)
        $ins = $db->prepare("INSERT INTO booking (student_id, session_id, status) VALUES (?, ?, 'confirmed')");
        $ins->execute([$student['student_id'], $session_id]);

        // 3. Seansın durumunu 'booked' yap (UPDATE)
        $upd = $db->prepare("UPDATE session SET status = 'booked' WHERE session_id = ?");
        $upd->execute([$session_id]);

        echo "<script>alert('Randevu başarıyla alındı!'); window.location.href='dashboard.php';</script>";
    } catch (Exception $e) {
        die("Hata: " . $e->getMessage());
    }
}

// İptal etme işlemi (DELETE)
if ($action == 'cancel') {
    $booking_id = $_GET['booking_id'];
    
    // Önce hangi seans olduğunu bulalım ki onu tekrar 'available' yapalım
    $find = $db->prepare("SELECT session_id FROM booking WHERE booking_id = ?");
    $find->execute([$booking_id]);
    $b = $find->fetch();

    // 1. Randevuyu sil
    $del = $db->prepare("DELETE FROM booking WHERE booking_id = ?");
    $del->execute([$booking_id]);

    // 2. Seansı tekrar boşa çıkar
    $upd = $db->prepare("UPDATE session SET status = 'available' WHERE session_id = ?");
    $upd->execute([$b['session_id']]);

    echo "<script>alert('Randevu iptal edildi!'); window.location.href='dashboard.php';</script>";
}
if ($action == 'add_session') {
    $date = $_POST['session_date'];
    $time = $_POST['start_time'];
    $user_id = $_SESSION['user_id'];

    try {
        // Giriş yapan kullanıcının tutor_id'sini çekiyoruz
        $t_query = $db->prepare("SELECT tutor_id FROM tutor WHERE user_id = ?");
        $t_query->execute([$user_id]);
        $tutor = $t_query->fetch();

        if ($tutor) {
            $ins = $db->prepare("INSERT INTO session (tutor_id, session_date, start_time, status) VALUES (?, ?, ?, 'available')");
            $ins->execute([$tutor['tutor_id'], $date, $time]);
            echo "<script>alert('Müsaitlik saati başarıyla eklendi!'); window.location.href='dashboard.php';</script>";
        }
    } catch (Exception $e) {
        die("Hata: " . $e->getMessage());
    }
}
if ($action == 'delete_session') {
    $session_id = $_GET['id'];
    
    try {
        $del = $db->prepare("DELETE FROM session WHERE session_id = ? AND status = 'available'");
        $del->execute([$session_id]);
        echo "<script>alert('Müsait seans başarıyla silindi.'); window.location.href='dashboard.php';</script>";
    } catch (Exception $e) {
        die("Hata: " . $e->getMessage());
    }
}
?>