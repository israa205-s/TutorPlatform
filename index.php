<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Giriş Yap - Tutor Randevu Sistemi</title>
    <style>
        /* Burası CSS bölümü: Sayfamızın görünümünü güzelleştirir */
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .login-container { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1); width: 300px; }
        h2 { text-align: center; color: #333; }
        input { width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #ddd; border-radius: 5px; box-sizing: border-box; }
        button { width: 100%; padding: 10px; background-color: #28a745; border: none; color: white; border-radius: 5px; cursor: pointer; }
        button:hover { background-color: #218838; }
    </style>
</head>
<body>

<div class="login-container">
    <h2>Sisteme Giriş</h2>
    <form action="login_kontrol.php" method="POST" id="loginForm">
        <input type="email" name="email" id="email" placeholder="E-posta Adresiniz" required>
        <input type="password" name="password" id="password" placeholder="Şifreniz" required>
        <button type="submit">Giriş Yap</button>
    </form>
</div>

<script>
    // Burası JS (JavaScript) bölümü: Form boş mu diye kontrol eder (Hocanın istediği JS doğrulaması)
    document.getElementById('loginForm').onsubmit = function(e) {
        let email = document.getElementById('email').value;
        let password = document.getElementById('password').value;

        if (email === "" || password === "") {
            alert("Lütfen tüm alanları doldurun!");
            e.preventDefault(); // Formun gönderilmesini durdurur
        }
    };
</script>

</body>
</html>

        