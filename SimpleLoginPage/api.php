<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

// Gelen POST verilerini al
$postData = file_get_contents("php://input");
$request = json_decode($postData, true);

if (isset($_GET['action'])) {
    $action = $_GET['action'];

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

    switch ($action) {
        case 'login':
            if (isset($request['email']) && isset($request['password'])) {
                $email = $request['email'];
                $password = md5($request['password']);

                // SQL sorgusu - Örnek olarak şifre doğrulama için PDO veya şifreleme kullanılmalıdır.
                $sql = "SELECT * FROM users WHERE email = '$email' AND password = '$password'";

                // Sorguyu çalıştırma
                $result = $conn->query($sql);

                // Sonucu kontrol et
                if ($result->num_rows > 0) {
                    // Eşleşen bir kayıt varsa
                    $token = generateToken(); // Oturum tokeni oluştur
                    echo json_encode(["success" => true, "message" => "Login successful!", "token" => $token]);
                } else {
                    // Eşleşen bir kayıt yoksa
                    echo json_encode(["success" => false, "message" => "Invalid email or password."]);
                }
            } else {
                // Geçersiz giriş
                echo json_encode(["success" => false, "message" => "Invalid input"]);
            }
            break;

        case 'register':
            if (isset($request['name']) && isset($request['surname']) && isset($request['email']) && isset($request['password'])) {
                $name = $request['name'];
                $surname = $request['surname'];
                $email = $request['email'];
                $password = md5($request['password']);

                // SQL sorgusu - Örnek olarak şifreleme veya PDO kullanılmalıdır.
                $preSql = "SELECT email FROM users  WHERE email = '$email'";
                if($conn->query($preSql) == TRUE){
                    echo json_encode(["success" => false, "message" => "E-mail can't be duplicated!"]);
                }
                else{
                    $sql = "INSERT INTO users (name, surname, email, password) VALUES ('$name', '$surname', '$email', '$password')";
                    // Sorguyu çalıştırma
                    if ($conn->query($sql) === TRUE) {
                        echo json_encode(["success" => true, "message" => "Registration successful!"]);
                    } else {
                        echo json_encode(["success" => false, "message" => "Error: " . $sql . "<br>" . $conn->error]);
                    }
                }              
            } else {
                // Geçersiz kayıt
                echo json_encode(["success" => false, "message" => "Invalid input"]);
            }
            break;

        default:
            echo json_encode(["success" => false, "message" => "Invalid action"]);
            break;
    }

    // Bağlantıyı kapat
    $conn->close();
} else {
    // Eylem belirtilmemiş
    echo json_encode(["success" => false, "message" => "Action not specified"]);
}

// Örnek oturum tokeni oluşturma fonksiyonu
function generateToken() {
    return md5(uniqid(rand(), true));
}
?>
