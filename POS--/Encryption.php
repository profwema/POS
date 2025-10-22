<?php
class Encryption {

    public  function safe_b64encode($string) {
        return urlencode($string);
    }

    public function safe_b64decode($string) {
        return urldecode($value);
    }

    public  function encode($value){ 
        return urlencode($value);
    }

    public function decode($value){
        return urldecode($value);
    }


    public  function encodeConstant($value){ 
        return urlencode($value);
    }

    public function decodeConstant($value){
        return urldecode($value);
    }
    
    
    
    
    /////////////////////////////////
    
    
    public  function encode_1($value){ 
        if(!$value){return false;}
        $text = $value;
        $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
        $crypttext = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $_SESSION['key'], $text, MCRYPT_MODE_ECB, $iv);
        return trim($this->safe_b64encode($crypttext)); 
    }

    public function decode_1($value){
        if(!$value){return false;}
        $crypttext = $this->safe_b64decode($value); 
        $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
        $decrypttext = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $_SESSION['key'], $crypttext, MCRYPT_MODE_ECB, $iv);
        return trim($decrypttext);
    }


    public  function encodeConstant_1($value){ 
        if(!$value){return false;}
        $text = $value;
        $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
        $crypttext = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $_SESSION['key_constant'], $text, MCRYPT_MODE_ECB, $iv);
        return trim($this->safe_b64encode($crypttext)); 
    }

    public function decodeConstant_1($value){
        if(!$value){return false;}
        $crypttext = $this->safe_b64decode($value); 
        $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
        $decrypttext = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $_SESSION['key_constant'], $crypttext, MCRYPT_MODE_ECB, $iv);
        return trim($decrypttext);
    }
}

?>