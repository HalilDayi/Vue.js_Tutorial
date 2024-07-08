<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

// Gelen POST verilerini al
$postData = file_get_contents("php://input");
$request = json_decode($postData, true);

if (isset($request['email']) && isset($request['password'])) {
    $email = $request['email'];
    $password = $request['password'];

    // Veritabanı bağlantısı ve sorgu işlemleri
    $servername = "localhost";
    $username = "root";
    $password_db = ""; // MySQL şifreniz
    $dbname = "simple_login"; // Veritabanı adı

    // MySQL bağlantısını oluşturma
    $conn = new mysqli($servername, $username, $password_db, $dbname);

    // Bağlantıyı kontrol et
    if ($conn->connect_error) {
        die(json_encode(["success" => false, "message" => "Connection failed: " . $conn->connect_error]));
    }

    // SQL sorgusu
    $sql = "SELECT * FROM users WHERE email = '$email' AND password = '$password'";

    // Sorguyu çalıştırma
    $result = $conn->query($sql);

    // Sonucu kontrol et
    if ($result->num_rows > 0) {
        // Eşleşen bir kayıt varsa
        echo json_encode(["success" => true, "message" => "Login successful!"]);
    } else {
        // Eşleşen bir kayıt yoksa
        echo json_encode(["success" => false, "message" => "Invalid email or password."]);
    }

    // Bağlantıyı kapat
    $conn->close();
} else {
    // Geçersiz giriş
    echo json_encode(["success" => false, "message" => "Invalid input"]);
}
?>