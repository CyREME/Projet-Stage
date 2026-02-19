<?php
include_once __DIR__ . '/Config.php';

/**
 * Chiffre une chaîne de caractères
 */
function encryptData($data) {
    $method = "aes-256-ctr";
    $iv_length = openssl_cipher_iv_length($method);
    $iv = openssl_random_pseudo_bytes($iv_length); // Vecteur d'initialisation unique
    
    $encrypted = openssl_encrypt($data, $method, ENCRYPTION_KEY, 0, $iv);
    
    // On stocke l'IV avec la donnée (séparés par :) car il est nécessaire pour déchiffrer
    return base64_encode($iv . ":" . $encrypted);
}

/**
 * Déchiffre une chaîne de caractères
 */
function decryptData($data) {
    $method = "aes-256-ctr";
    $data = base64_decode($data);
    $iv_length = openssl_cipher_iv_length($method);
    
    $parts = explode(":", $data, 2);
    if (count($parts) < 2) return $data; // Donnée non chiffrée
    
    $iv = $parts[0];
    $encrypted = $parts[1];
    
    return openssl_decrypt($encrypted, $method, ENCRYPTION_KEY, 0, $iv);
}
?>