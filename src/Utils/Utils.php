<?php
/**
 * This file is part of GTrack.
 *
 * @author walangkaji <walangkaji@outlook.com>
 */

namespace GTrack\Utils;

/**
 * Just Global Function
 */
class Utils
{
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
        if (is_null($date)) {
            return null;
        }

        return date('d-m-Y H:i', $timestamp ? $date : strtotime($date));
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
    public static function getBetween($content, $start, $end)
    {
        $r = explode($start, $content);
        if (isset($r[1])) {
            $r = explode($end, $r[1]);

            return $r[0];
        }

        return '';
    }

    /**
     * Make object from array
     *
     * @param array $array
     *
     * @return object
     */
    public static function decode($array, $assoc = false)
    {
        return json_decode(json_encode($array), $assoc);
    }

    /**
     * Error message format easy use
     *
     * @param bool   $err
     * @param string $msg
     *
     * @return array
     */
    public static function errMsg($err, $msg)
    {
        return [
            'error'   => $err,
            'message' => $msg,
        ];
    }

    /**
     * Format ekspedisi info
     *
     * @param object $object
     *
     * @return array
     */
    public static function ekspedisiInfo($object)
    {
        return [
            'name' => $object->ekspedisi['name'],
            'site' => $object->ekspedisi['site']
        ];
    }

    /**
     * Format resi lion parcel
     *
     * @param string $resi
     *
     * @return string
     */
    public static function formatLionResi($resi)
    {
        if (strpos($resi, '-') !== false) {
            $resi = str_replace('-', '', $resi);
        }

        $resi = substr($resi, 0, 2) . '-' . substr($resi, 2);
        $resi = substr($resi, 0, 5) . '-' . substr($resi, 5);

        return $resi;
    }

    /**
     * Search text if exist in a content string
     *
     * @param string $search  text yang dicari
     * @param string $content content string
     *
     * @return bool
     */
    public static function exist($search, $content)
    {
        if (strpos($content, $search) !== false) {
            return true;
        }

        return false;
    }
}
