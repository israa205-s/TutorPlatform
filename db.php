<?php
try {
    // SQLite veritabanı dosyamıza bağlanıyoruz
    $db = new PDO("sqlite:fiiinaaaaalll_db_project.db");
    
    // Hata ayıklama modunu açıyoruz
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Bağlantı başarılıysa (test için kullandım)
     //echo "Veritabanına başarıyla bağlanıldı!"; 
} catch (PDOException $e) {
    // Bağlantı hatası olursa ekrana yazdır
    die("Bağlantı hatası: " . $e->getMessage());
}
        //localhost/tutor_project/db.php adresini tarayıcıda arat ve bağlantı kuruldu mu gör!
?>