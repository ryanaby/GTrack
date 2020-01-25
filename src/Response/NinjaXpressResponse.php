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
class NinjaXpressResponse
{
    public static $messageStatus;
    public static $tanggal_kirim;
    public static $tanggal_terima;

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
        $data['eks']            = 'NINJA XPRESS';
        $data['site']           = 'https://www.ninjaxpress.co';

        if ($isError) {
            $data['error']      = $isError;
            $data['message']    = self::$messageStatus;

            return json_decode(json_encode($data));
        }

        $response               = $response->orders[0];
        $history                = self::getHistory($response);
        $data['error']          = $isError;
        $data['message']        = self::$messageStatus;
        $data['info']           = [
            'id'                => $response->id,
            'no_awb'            => $response->tracking_id,
            'service'           => $response->service_type,
            'status'            => ($response->status == 'Completed') ? 'DELIVERED' : strtoupper($response->status),
            'tanggal_kirim'     => self::$tanggal_kirim,
            'tanggal_terima'    => self::$tanggal_terima,
            'asal_pengiriman'   => null,
            'tujuan_pengiriman' => $response->to_city,
            'harga'             => null,
            'berat'             => null, // gram
            'catatan'           => null,
        ];
        $data['pengirim']       = [
            'nama'              => trim(strtoupper($response->from_name)),
            'phone'             => null,
            'kota'              => null,
            'alamat1'           => null,
            'alamat2'           => null,
            'alamat3'           => null,
        ];
        $data['penerima']       = [
            'nama'              => null,
            'nama_penerima'     => null,
            'phone'             => null,
            'kota'              => null,
            'alamat1'           => null,
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
        if (empty($response)) {
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

        foreach ($response->events as $k => $v) {
            $time = substr($v->time, 0, -3);

            if (strpos($v->description, ' - ') !== false) {
                $posisi = strtoupper(preg_replace('/(.*) - (.*)/', '$2', $v->description));
            } else {
                $posisi = null;
            }

            if ($v->description == 'Order dibuat') {
                self::$tanggal_kirim  = GlobalFunction::setDate($time, true);
            }

            if (strpos($v->description, 'Telah berhasil dijemput dari') !== false) {
                self::$tanggal_kirim  = GlobalFunction::setDate($time, true);
            }

            if ($v->description == 'Parsel telah berhasil dikirimkan') {
                self::$tanggal_terima = GlobalFunction::setDate($time, true);
            }

            $history[$k]['tanggal'] = GlobalFunction::setDate($time, true);
            $history[$k]['posisi']  = $posisi;
            $history[$k]['message'] = $v->description;
        }

        return $history;
    }
}
