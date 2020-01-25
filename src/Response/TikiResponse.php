<?php
/**
 * Global Tesla - globaltesla.com
 *
 * @author     Global Tesla <dev@globaltesla.com>
 * @copyright  2019 Global Tesla
 */

namespace GTrack\Response;

use \GTrack\GlobalFunction;

/**
 * Formatting response
 */
class TikiResponse
{
    public static $statusDelivery;
    public static $penerima;
    public static $tglTerima;
    public static $messageStatus;

    /**
     * Format result yang diproses
     *
     * @param object $response response dari request
     *
     * @return object
     */
    public static function result($response)
    {
        $data                   = [];
        $isError                = self::isError($response);
        $data['eks']            = 'TIKI';
        $data['site']           = 'https://tiki.id';

        if ($isError) {
            $data['error']      = $isError;
            $data['message']    = self::$messageStatus;

            return json_decode(json_encode($data));
        }

        $info                   = $response->info;
        $history                = self::getHistory($response);
        $data['error']          = $isError;
        $data['message']        = self::$messageStatus;
        $data['info']           = [
            'id'                => null,
            'no_awb'            => $info->cnno,
            'service'           => $info->product,
            'status'            => self::$statusDelivery,
            'tanggal_kirim'     => GlobalFunction::setDate($info->sys_created_on),
            'tanggal_terima'    => self::$tglTerima,
            'asal_pengiriman'   => $info->consignor_address,
            'tujuan_pengiriman' => $info->destination_city_name,
            'harga'             => (int) $info->shipment_fee,
            'berat'             => (int) $info->weight * 1000, // gram
            'catatan'           => null,
        ];
        $data['pengirim']       = [
            'nama'              => rtrim(strtoupper($info->consignor_name)),
            'phone'             => null,
            'kota'              => rtrim($info->consignor_address),
            'alamat1'           => Globalfunction::setIfNull($info->consignor_address),
            'alamat2'           => null,
            'alamat3'           => null,
        ];
        $data['penerima']       = [
            'nama'              => rtrim(strtoupper($info->consignee_name)),
            'nama_penerima'     => self::$penerima,
            'phone'             => null,
            'kota'              => $info->destination_city_name,
            'alamat1'           => Globalfunction::setIfNull($info->consignee_address),
            'alamat2'           => null,
            'alamat3'           => null,
        ];
        $data['history']        = $history;

        return json_decode(json_encode($data));
    }

    /**
     * Get status dan message
     *
     * @param object $response response dari request
     *
     * @return bool
     */
    private static function isError($response)
    {
        if (empty($response->info)) {
            self::$messageStatus = 'No AWB tidak ditemukan.';

            return true;
        } else {
            self::$messageStatus = 'success';

            return false;
        }
    }

    /**
     * Compile history dengan format yang sudah disesuaikan
     *
     * @param object $response response dari request
     *
     * @return array
     */
    private static function getHistory($response)
    {
        $history = [];

        foreach (array_reverse($response->history) as $k => $v) {
            if ($v->status == 'POD 01') {
                self::$statusDelivery   = 'DELIVERED';
                self::$penerima         = preg_replace('/(.*) RECEIVED BY: (.*)/', '$2', $v->noted);
                self::$tglTerima        = GlobalFunction::setDate($v->entry_date);
            } else {
                self::$statusDelivery   = 'ON PROCESS';
                self::$penerima         = null;
                self::$tglTerima        = null;
            }

            $history[$k]['tanggal']     = GlobalFunction::setDate($v->entry_date);
            $history[$k]['posisi']      = rtrim($v->entry_name);
            $history[$k]['message']     = $v->noted;
        }

        return $history;
    }
}
