<?php
require "config.php";
require "functions.php";

$baseURL1 = "/HWPProjektMARCELLO/PHP/index.php"; //Gabor url
$baseURL2 = "/HWP_2024/HWPProjektMARCELLO/PHP/index.php"; //Mate url

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");
//header('Content-Type: application/json');

error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['CONTENT_TYPE'] === 'application/json') {
    $data = json_decode(file_get_contents('php://input'), true);
    $username = $data['username'] ?? '';
    $password = $data['password'] ?? '';
} else {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
}

if (empty($username) || empty($password)) {
    echo "
        <script>
            alert('Both fields are required to login!');
            window.location.href = 'login.php';
        </script>
    ";
//    echo json_encode(['success' => false, 'message' => '']);
    exit();
}

if ($username === 'admin@mmm.com' && $password === 'admin') {
    echo json_encode([
        'success' => true,
        'message' => 'Admin login successful.',
        'userId' => 0 // Or any special admin ID
    ]);
    exit();
}

try {
    $result = loginUser($username, $password, $dbHost, $dbName, $dbUser, $dbPass);

    if ($result['success']) {
        $userId = $result['user_id'];

        // Detect if this is a browser request (not JSON)
        $isJsonRequest = ($_SERVER['CONTENT_TYPE'] === 'application/json');

        if ($isJsonRequest) {
            // API / React Native: Return JSON
            echo json_encode([
                'success' => true,
                'userId' => $userId,
                'message' => 'Login successful.'
            ]);
        } else {
            // Browser request: Redirect to homepage
            session_start();
            $_SESSION['user_id'] = $userId;
            header("Location: ".$baseURL1);
        }
        exit();
    }
    else
    {
        echo "
            <script>
                alert(" . json_encode($result['message'] ?? 'Ismeretlen hiba.') . ");
                window.location.href = 'login.php';
            </script>
";
//        echo json_encode(['success' => false, 'message' => $result['message'] ?? 'Invalid credentials.']);
        exit();
    }
} catch (Exception $e) {
    echo "
        <script>
            alert('An error occurred, please try again later.');
            window.location.href = 'login.php';
        </script>
    ";
//    echo json_encode(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()]);
    exit();
}

