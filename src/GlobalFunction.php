<?php
/**
 * Global Tesla - globaltesla.com
 *
 * @author     Global Tesla <dev@globaltesla.com>
 * @copyright  2019 Global Tesla
 */

namespace GTrack;

/**
 * Just Global Function
 */
class GlobalFunction
{

    /**
     * Generate random string
     *
     * @param int  $length   Panjang karakter
     * @param bool $strOnly  Yang ditampilkan hanya string
     * @param bool $smallStr Yang ditampilkan hanya huruf kecil & digit
     *
     * @return string
     */
    public static function randomStr($length = 10, $strOnly = false, $smallStr = false)
    {
        $char = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

        if ($strOnly) {
            $char   = preg_replace('/[\d]/', '$1', $char);
        }

        if ($smallStr) {
            $char   = preg_replace('/[A-Z]/', '$1', $char);
        }

        $charLength = strlen($char);
        $randStr    = '';

        for ($i = 0; $i < $length; $i++) {
            $randStr .= $char[rand(0, $charLength - 1)];
        }

        return $randStr;
    }

    /**
     * Untuk format waktu
     *
     * @param string $date      tanggalnya
     * @param bool   $timestamp true untuk mode timestamp
     *
     * @return string
     */
    public static function setDate($date, $timestamp = false)
    {
        if ($timestamp) {
            return date('d-m-Y H:i', $date);
        }

        return date('d-m-Y H:i', strtotime($date));
    }

    /**
     * Set data jika null
     *
     * @param string $value String yang akan di proses
     *
     * @return mixed
     */
    public static function setIfNull($value)
    {
        return !is_null($value) ? rtrim($value) : null;
    }

    /**
     * Untuk cari string diantara string
     *
     * @param string $content contentnya
     * @param string $start   awalan
     * @param string $end     akhiran
     *
     * @return string
     */
    public static function GetBetween($content, $start, $end)
    {
        $r = explode($start, $content);
        if (isset($r[1])) {
            $r = explode($end, $r[1]);

            return $r[0];
        }

        return '';
    }
}
