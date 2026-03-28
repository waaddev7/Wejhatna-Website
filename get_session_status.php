<?php
session_start();
header('Content-Type: application/json');

$response = [
    'loggedin' => false,
    'userName' => '',
    'isAdmin'  => false
];

if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    $response['loggedin'] = true;
    $response['userName'] = htmlspecialchars($_SESSION['user_name']);
    if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin') {
        $response['isAdmin'] = true;
    }
}

echo json_encode($response);
?>