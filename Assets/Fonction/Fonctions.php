<?php
include_once __DIR__ . '/Config.php';

/**
 * Chiffre une chaîne de caractères
 */
function encryptData($data) {
    if (empty($data)) return "";
    $method = "aes-256-ctr";
    $iv_length = openssl_cipher_iv_length($method);
    $iv = openssl_random_pseudo_bytes($iv_length);

    $encrypted = openssl_encrypt($data, $method, ENCRYPTION_KEY, OPENSSL_RAW_DATA, $iv);

    // On colle l'IV devant les données brutes, puis on encode le TOUT en base64
    return base64_encode($iv . $encrypted);
}

/**
 * Déchiffre une chaîne de caractères
 */
function decryptData($data) {
    if (empty($data)) return "";
    $method = "aes-256-ctr";
    $data = base64_decode($data);
    $iv_length = openssl_cipher_iv_length($method);

    // On extrait les 16 premiers octets pour l'IV
    $iv = substr($data, 0, $iv_length);
    // Le reste correspond aux données chiffrées
    $encrypted = substr($data, $iv_length);

    return openssl_decrypt($encrypted, $method, ENCRYPTION_KEY, OPENSSL_RAW_DATA, $iv);
}
?>