<?php
if (isset($_GET['ip'])) {
    $ip = $_GET['ip'];
    $token = 'ded9477be1307b';
    $url = "https://ipinfo.io/{$ip}/json?token={$token}";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);

    header('Content-Type: application/json');
    echo $response;
} else {
    echo json_encode(['error' => 'No IP provided']);
}
?>
