<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

$user_sorgu = $db->prepare("SELECT email FROM users WHERE user_id = :id");
$user_sorgu->execute([':id' => $user_id]);
$current_user = $user_sorgu->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Panel - Tutor Sistemi</title>
    <style>
        /* TASARIMI BURAYA GÖMDÜK - KAÇIŞI YOK :) */
        body { font-family: 'Segoe UI', Tahoma, sans-serif; margin: 0; display: flex; background: #f0f2f5; color: #1c1e21; }
        .sidebar { width: 260px; background: #1877f2; color: white; height: 100vh; padding: 30px 20px; position: fixed; box-shadow: 2px 0 5px rgba(0,0,0,0.1); }
        .main-content { flex: 1; margin-left: 300px; padding: 40px; }
        .card { background: white; padding: 25px; border-radius: 12px; box-shadow: 0 2px 12px rgba(0,0,0,0.08); margin-bottom: 25px; }
        h2, h3 { margin-top: 0; color: #1877f2; }
        .sidebar h2 { color: white; border-bottom: 1px solid rgba(255,255,255,0.2); padding-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; background: white; border-radius: 8px; overflow: hidden; }
        th { background-color: #f0f2f5; color: #65676b; padding: 15px; text-align: left; font-size: 13px; text-transform: uppercase; }
        td { padding: 15px; border-bottom: 1px solid #eee; font-size: 15px; }
        .btn-primary { display: inline-block; background-color: #1877f2; color: white; padding: 12px 20px; text-decoration: none; border-radius: 8px; font-weight: bold; border: none; cursor: pointer; transition: 0.2s; }
        .btn-primary:hover { background-color: #166fe5; }
        .logout-btn { display: block; margin-top: 30px; color: #ffeb3b; text-decoration: none; font-weight: bold; }
        input, select { padding: 10px; border: 1px solid #ddd; border-radius: 6px; margin-right: 10px; }
    </style>
</head>
<body>

<div class="sidebar">
    <h2>🎓 Tutor App</h2>
    <p>Hoş geldin, <br><strong><?php echo $current_user['email']; ?></strong></p>
    <p>Statü: <span style="background: rgba(255,255,255,0.2); padding: 2px 8px; border-radius: 4px;"><?php echo ucfirst($role); ?></span></p>
    <hr style="opacity: 0.2;">
    <a href="logout.php" class="logout-btn">✕ Güvenli Çıkış</a>
</div>

<div class="main-content">
    
    <?php if ($role == 'student'): ?>
        <div class="card" style="text-align: center; background: #e7f3ff;">
            <h3>Yeni Bir Şeyler Öğrenmeye Hazır mısın?</h3>
            <a href="randevu_al.php" class="btn-primary">🚀 Hemen Randevu Al</a>
        </div>
    <?php endif; ?>

    <?php if ($role == 'tutor'): ?>
        <div class="card" style="border-left: 6px solid #42b72a;">
            <h3>🗓️ Yeni Müsaitlik Zamanı Oluştur</h3>
            <form action="islem_yap.php?action=add_session" method="POST" style="display: flex; align-items: flex-end;">
                <div>
                    <label style="font-size: 12px; font-weight: bold;">TARİH</label><br>
                    <input type="date" name="session_date" required>
                </div>
                <div>
                    <label style="font-size: 12px; font-weight: bold;">SAAT</label><br>
                    <input type="time" name="start_time" required>
                </div>
                <button type="submit" class="btn-primary" style="background: #42b72a;">Sisteme Ekle</button>
            </form>
        </div>

        <div class="card">
            <h3>🔓 Boş Seanslarınız</h3>
            <?php
            $empty_sessions = $db->prepare("SELECT session_id, session_date, start_time FROM session WHERE tutor_id = (SELECT tutor_id FROM tutor WHERE user_id = :uid) AND status = 'available'");
            $empty_sessions->execute([':uid' => $user_id]);
            $sessions = $empty_sessions->fetchAll(PDO::FETCH_ASSOC);

            if (count($sessions) > 0) {
                echo "<table><tr><th>Tarih</th><th>Saat</th><th>İşlem</th></tr>";
                foreach ($sessions as $s) {
                    echo "<tr>
                            <td>{$s['session_date']}</td>
                            <td>{$s['start_time']}</td>
                            <td><a href='islem_yap.php?action=delete_session&id={$s['session_id']}' style='color:#fa3e3e; text-decoration:none; font-weight:bold;'>Sil</a></td>
                          </tr>";
                }
                echo "</table>";
            } else { echo "<p style='color: #65676b;'>Henüz boş seans eklemediniz.</p>"; }
            ?>
        </div>
    <?php endif; ?>

    <div class="card">
        <h3>📅 Mevcut Randevular</h3>
        <?php
        if ($role == 'student') {
            $sql = "SELECT b.booking_id, s.session_date, s.start_time, b.status, u.email as partner 
                    FROM booking b 
                    JOIN session s ON b.session_id = s.session_id 
                    JOIN tutor t ON s.tutor_id = t.tutor_id
                    JOIN users u ON t.user_id = u.user_id
                    WHERE b.student_id = (SELECT student_id FROM student WHERE user_id = :uid)";
        } else {
            $sql = "SELECT b.booking_id, s.session_date, s.start_time, b.status, u.email as partner 
                    FROM booking b 
                    JOIN session s ON b.session_id = s.session_id 
                    JOIN student st ON b.student_id = st.student_id
                    JOIN users u ON st.user_id = u.user_id
                    WHERE s.tutor_id = (SELECT tutor_id FROM tutor WHERE user_id = :uid)";
        }

        $stmt = $db->prepare($sql);
        $stmt->execute([':uid' => $user_id]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (count($rows) > 0) {
            echo "<table><tr><th>ID</th><th>Partner</th><th>Tarih</th><th>Saat</th><th>Durum</th><th>İşlem</th></tr>";
            foreach ($rows as $row) {
                echo "<tr>
                        <td>#{$row['booking_id']}</td>
                        <td>{$row['partner']}</td>
                        <td>{$row['session_date']}</td>
                        <td>{$row['start_time']}</td>
                        <td><span style='background:#e1f5fe; padding:4px 8px; border-radius:4px;'>{$row['status']}</span></td>
                        <td><a href='randevu_sil.php?id={$row['booking_id']}' style='color:#fa3e3e;'>İptal</a></td>
                      </tr>";
            }
            echo "</table>";
        } else { echo "<p style='color: #65676b;'>Görüntülenecek randevu bulunamadı.</p>"; }
        ?>
    </div>

    <button onclick="dersDetayGetir(1)" class="btn-primary" style="background: #606770; font-size: 12px; padding: 8px 15px;">
        🔍 AJAX Bilgi Testi
    </button>
</div>

<script>
function dersDetayGetir(id) {
    fetch('get_major_info.php?id=' + id)
        .then(response => response.json())
        .then(data => {
            alert("Bölüm Detayı:\n----------------\nAdı: " + data.major_name + "\nKodu: " + data.major_id);
        })
        .catch(error => alert("Veri çekilemedi!"));
}
</script>
</body>
</html>