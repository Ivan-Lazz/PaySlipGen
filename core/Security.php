<?php

namespace Core;

class Security
{
    /**
     * Hash a password
     *
     * @param string $password
     * @return string
     */
    public static function hashPassword($password)
    {
        return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
    }
    
    /**
     * Verify a password against a hash
     *
     * @param string $password
     * @param string $hash
     * @return bool
     */
    public static function verifyPassword($password, $hash)
    {
        return password_verify($password, $hash);
    }
    
    /**
     * Generate a random token
     *
     * @param int $length
     * @return string
     */
    public static function generateToken($length = 32)
    {
        return bin2hex(random_bytes($length));
    }
    
    /**
     * Safely encrypt data using sodium
     *
     * @param string $data
     * @param string $key
     * @return string
     */
    public static function encrypt($data, $key = null)
    {
        if ($key === null) {
            $key = config('app.key');
        }
        
        $key = sodium_crypto_generichash(
            $key,
            '',
            SODIUM_CRYPTO_SECRETBOX_KEYBYTES
        );
        
        $nonce = random_bytes(SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);
        $ciphertext = sodium_crypto_secretbox($data, $nonce, $key);
        
        return base64_encode($nonce . $ciphertext);
    }
    
    /**
     * Decrypt data that was encrypted with the encrypt method
     *
     * @param string $encrypted
     * @param string $key
     * @return string|false
     */
    public static function decrypt($encrypted, $key = null)
    {
        if ($key === null) {
            $key = config('app.key');
        }
        
        $key = sodium_crypto_generichash(
            $key,
            '',
            SODIUM_CRYPTO_SECRETBOX_KEYBYTES
        );
        
        $decoded = base64_decode($encrypted);
        if ($decoded === false) {
            return false;
        }
        
        if (strlen($decoded) < SODIUM_CRYPTO_SECRETBOX_NONCEBYTES) {
            return false;
        }
        
        $nonce = mb_substr($decoded, 0, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES, '8bit');
        $ciphertext = mb_substr($decoded, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES, null, '8bit');
        
        $plaintext = sodium_crypto_secretbox_open($ciphertext, $nonce, $key);
        
        if ($plaintext === false) {
            return false;
        }
        
        return $plaintext;
    }
}