<?php

namespace AgeGate\Utility;

use Exception;

class Encrypt
{
    private $secretKey;
    private $secretIv;
    private $key;
    private $iv;

    public function __construct()
    {
        $this->secretKey = $_ENV['AGE_GATE_SECRET'] ?? get_option('age_gate_encrypt_key', false) ?: 'nUE4At3gpBqoFnVgG4Fd8zMR3DgTvCxa';
        $this->secretIv = $_ENV['AGE_GATE_IV'] ?? get_option('age_gate_encrypt_secret', false) ?: 'OglthbwSgkCgw4yw2CauWUXXUVAHRQyI';
        $this->key = hash('sha256', $this->secretKey);
        $this->iv = substr(hash('sha256', $this->secretIv), 0, 16);
    }

    public function encrypt(string $string) : string
    {
        try {
            return openssl_encrypt($string, "AES-256-CBC", $this->key, 0, $this->iv);
        } catch (Exception $e) {
            return base64_encode($string);
        }
    }

    public function decrypt(string $string) : string
    {
        try {
            return openssl_decrypt($string, "AES-256-CBC", $this->key, 0, $this->iv);
        } catch (Exception $e) {
            base64_decode($string);
        }
    }
}
