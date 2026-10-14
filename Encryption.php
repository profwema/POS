<?php
/**
 * Encryption class - PHP 8.2 compatible using OpenSSL
 */
class Encryption {

    public function safe_b64encode($string) {
        return urlencode((string)($string ?? ''));
    }

    public function safe_b64decode($string) {
        return urldecode((string)($string ?? ''));
    }

    public function encode($value){ 
        return urlencode((string)($value ?? ''));
    }

    public function decode($value){
        return urldecode((string)($value ?? ''));
    }

    public function encodeConstant($value){ 
        return urlencode((string)($value ?? ''));
    }

    public function decodeConstant($value){
        return urldecode((string)($value ?? ''));
    }

    protected function getKey($key = null) {
        $raw = $key ?? ($_SESSION['key'] ?? 'pos_default_secret_key_2026');
        return substr(hash('sha256', (string)$raw, true), 0, 32);
    }

    protected function getIV($key = null) {
        $raw = $key ?? ($_SESSION['key'] ?? 'pos_default_secret_key_2026');
        return substr(hash('md5', (string)$raw), 0, 16);
    }

    public function encode_1($value){ 
        if(!$value){return false;}
        $text = (string)$value;
        $key = $this->getKey($_SESSION['key'] ?? null);
        $iv = $this->getIV($_SESSION['key'] ?? null);
        $crypttext = openssl_encrypt($text, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);
        return trim($this->safe_b64encode(base64_encode($crypttext))); 
    }

    public function decode_1($value){
        if(!$value){return false;}
        $raw = base64_decode($this->safe_b64decode($value)); 
        $key = $this->getKey($_SESSION['key'] ?? null);
        $iv = $this->getIV($_SESSION['key'] ?? null);
        $decrypttext = openssl_decrypt($raw, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);
        return trim((string)$decrypttext);
    }

    public function encodeConstant_1($value){ 
        if(!$value){return false;}
        $text = (string)$value;
        $key = $this->getKey($_SESSION['key_constant'] ?? null);
        $iv = $this->getIV($_SESSION['key_constant'] ?? null);
        $crypttext = openssl_encrypt($text, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);
        return trim($this->safe_b64encode(base64_encode($crypttext))); 
    }

    public function decodeConstant_1($value){
        if(!$value){return false;}
        $raw = base64_decode($this->safe_b64decode($value)); 
        $key = $this->getKey($_SESSION['key_constant'] ?? null);
        $iv = $this->getIV($_SESSION['key_constant'] ?? null);
        $decrypttext = openssl_decrypt($raw, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);
        return trim((string)$decrypttext);
    }
}
?>