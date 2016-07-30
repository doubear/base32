<?php namespace Base32;

class Base32
{
    /**
     * @type string
     */
    const ALPHABET = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';

    /**
     * Base32 Encoder
     *
     * @param  string $str
     * @return string
     */
    public static function encode($str)
    {
        static $map;

        // Generates the chars map
        if (is_null($map)) {
            $map = str_split(self::ALPHABET);
        }

        // Returns null if no data given
        if (!$str) {
            return null;
        }

        // Process the given str
        $str = str_split(strval($str));
        $str = array_map(function ($char) {
            return str_pad(base_convert(strval(ord($char)), 10, 2), 8, '0', STR_PAD_LEFT);
        }, $str);

        $binary   = join('', $str);
        $fiveBits = str_split($binary, 5); // (000000 => 0) -> (011111 => 31)
        $maped    = array_map(function ($bit) use ($map) {
            $bit   = strval($bit);
            $bit   = str_pad($bit, 5, '0', STR_PAD_RIGHT);
            $index = base_convert($bit, 2, 10);
            return $map[$index];
        }, $fiveBits);

        $encoded = join('', $maped);
        $pad     = strlen($encoded) % 8;

        return $encoded . str_repeat('=', $pad ? 8 - $pad : 0);
    }

    /**
     * Base32 Decoder
     *
     * @param  string $str
     * @return string
     */
    public static function decode($str)
    {
        static $map;

        // Generates the chars map
        if (is_null($map)) {
            $map = str_split(self::ALPHABET);
            $map = array_flip($map);
        }

        // Returns null if no data given
        if (!$str) {
            return null;
        }

        $str      = rtrim($str, '=');
        $maped    = str_split($str);
        $fiveBits = array_map(function ($char) use ($map) {
            $index = $map[$char]; // Exp: ($char = A) => ($index = 0)
            return str_pad(decbin($index), 5, '0', STR_PAD_LEFT);
        }, $maped);

        $binary = join('', $fiveBits);
        $binary = str_split($binary, 8);
        $chars  = array_map(function ($bin) {
            $ascii = bindec($bin);
            return chr($ascii);
        }, $binary);

        return join('', $chars);
    }

}
