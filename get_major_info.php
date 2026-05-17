<?php
include 'db.php';

if(isset($_GET['id'])) {
    $id = $_GET['id'];
    $sorgu = $db->prepare("SELECT * FROM major WHERE major_id = ?");
    $sorgu->execute([$id]);
    $data = $sorgu->fetch(PDO::FETCH_ASSOC);

    // Veriyi JSON formatında gönderiyoruz (Hocanın istediği tam olarak bu!)
    echo json_encode($data);
}
?>