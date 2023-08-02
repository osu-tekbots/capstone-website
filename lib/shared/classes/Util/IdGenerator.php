<?php
namespace Util;

/**
 * Generates cryptographically secure random unique IDs for use with capstone website resources.
 */
class IdGenerator {

    /**
     * Generate a secure, random alphanumeric ID of the specified length. The default length is 16.
     *
     * @param integer $length the length of the ID in bytes (string characters)
     * @return string the generated ID
     */
    public static function generateSecureUniqueId($length = 16) {
        $token = "";
        $codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $codeAlphabet .= "abcdefghijklmnopqrstuvwxyz";
        $codeAlphabet .= "0123456789";
        $max = strlen($codeAlphabet);

        try {
            for ($i = 0; $i < $length; $i++) {
                $token .= $codeAlphabet[self::cryptoSecureRand(0, $max - 1)];
            }
        } catch (\Exception $e) {
            return null;
        }

        return $token;
    }

    /**
     * Generates a random number within the provided range. The number will be cryptographically strong.
     *
     * @param integer $min the lower bound on the range of the generated number (inclusive)
     * @param integer $max the upper bound on the range of the generated number (inclusive)
     * @return integer a cryptographically secure random number
     */
    private static function cryptoSecureRand($min, $max)
    {
        $range = $max - $min;
        if ($range < 1) return $min;
        $log = ceil(log($range, 2));
        $bytes = (int) ($log / 8) + 1; // length in bytes
        $bits = (int) $log + 1; // length in bits
        $filter = (int) (1 << $bits) - 1; // set all lower bits to 1
        do {
            $rnd = hexdec(bin2hex(openssl_random_pseudo_bytes($bytes)));
            $rnd = $rnd & $filter; // discard irrelevant bits
        } while ($rnd > $range);
        return $min + $rnd;
    }
}