<?php
/**
 * AES Encryption - PHP 8.2 Compatible using OpenSSL
 */
class AES {
  
  const M_CBC = 'cbc';
  const M_CFB = 'cfb';
  const M_ECB = 'ecb';
  const M_NOFB = 'nofb';
  const M_OFB = 'ofb';
  const M_STREAM = 'stream';
  
  protected $key;
  protected $cipher;
  protected $data;
  protected $mode;
  protected $IV;

  public function __construct($data = null, $key = null, $blockSize = null, $mode = null) {
    $this->setData($data);
    $this->setKey($key);
    $this->setBlockSize($blockSize);
    $this->setMode($mode);
    $this->setIV("");
  }
  
  public function setData($data) {
    $this->data = $data;
  }
  
  public function setKey($key) {
    $this->key = (string)($key ?? '');
  }
  
  public function setBlockSize($blockSize) {
    switch ($blockSize) {
      case 128:
        $this->cipher = 'AES-128-CBC';
        break;
      case 192:
        $this->cipher = 'AES-192-CBC';
        break;
      case 256:
      default:
        $this->cipher = 'AES-256-CBC';
        break;
    }
  }
  
  public function setMode($mode) {
    $this->mode = $mode ?: 'cbc';
  }
  
  public function validateParams() {
    return ($this->data !== null && $this->key !== null && $this->key !== '');
  }
  
  public function setIV($IV) {
    $this->IV = (string)($IV ?? '');
  }

  protected function getIV() {
    if (empty($this->IV)) {
      $this->IV = substr(hash('md5', (string)$this->key), 0, 16);
    }
    return substr(str_pad((string)$this->IV, 16, "\0"), 0, 16);
  }

  protected function getNormalizedKey() {
    return substr(hash('sha256', (string)$this->key, true), 0, 32);
  }
  
  public function encrypt() {
    if ($this->validateParams()) {
      $cipher = $this->cipher ?: 'AES-256-CBC';
      $key = $this->getNormalizedKey();
      $iv = $this->getIV();
      $encrypted = openssl_encrypt((string)$this->data, $cipher, $key, OPENSSL_RAW_DATA, $iv);
      return trim(base64_encode($encrypted));
    } else {
      throw new Exception('Invalid params!');
    }
  }

  public function decrypt() {
    if ($this->validateParams()) {
      $cipher = $this->cipher ?: 'AES-256-CBC';
      $key = $this->getNormalizedKey();
      $iv = $this->getIV();
      $raw = base64_decode((string)$this->data);
      $decrypted = openssl_decrypt($raw, $cipher, $key, OPENSSL_RAW_DATA, $iv);
      return trim((string)$decrypted);
    } else {
      throw new Exception('Invalid params!');
    }
  }
}
?>