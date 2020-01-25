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
class PosResponse
{
    public static $messageStatus;
    public static $tanggal_kirim;
    public static $yang_menerima;

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
        $data['eks']            = 'POS INDONESIA';
        $data['site']           = 'http://www.posindonesia.co.id';

        if ($isError) {
            $data['error']      = $isError;
            $data['message']    = self::$messageStatus;

            return json_decode(json_encode($data));
        }

        $info                   = self::getInfo($response);
        $history                = self::getHistory($response);

        $data['error']          = $isError;
        $data['message']        = self::$messageStatus;
        $data['info']           = [
            'id'                => null,
            'no_awb'            => $info->no_awb,
            'service'           => $info->service,
            'status'            => strtoupper($info->status_pengiriman),
            'tanggal_kirim'     => $info->tanggal_kirim,
            'tanggal_terima'    => $info->tanggal_terima,
            'asal_pengiriman'   => $info->asal_pengiriman,
            'tujuan_pengiriman' => $info->tujuan_pengiriman,
            'harga'             => null,
            'berat'             => $info->berat, // gram
            'catatan'           => $info->catatan,
        ];
        $data['pengirim']       = [
            'nama'              => strtoupper($info->nama_pengirim),
            'phone'             => $info->phone_pengirim,
            'kota'              => $info->kota_pengirim,
            'alamat1'           => $info->asal_pengiriman,
            'alamat2'           => null,
            'alamat3'           => null,
        ];
        $data['penerima']       = [
            'nama'              => strtoupper($info->nama_penerima),
            'nama_penerima'     => strtoupper(self::$yang_menerima),
            'phone'             => $info->phone_penerima,
            'kota'              => $info->kota_penerima,
            'alamat1'           => $info->tujuan_pengiriman,
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
        if (isset($response->api_callback->response)) {
            if (is_null($response->api_callback->response)) {
                self::$messageStatus = 'Data tidak ditemukan.';

                return true;
            } else {
                self::$messageStatus = 'success';

                return false;
            }
        }
        self::$messageStatus = 'Server error, Please try again';

        return true;
    }

    /**
     * Get info
     *
     * @param object $response response dari request
     *
     * @return object
     */
    private static function getInfo($response)
    {
        $data  = end($response->api_callback->response->data);
        $ket   = explode('~~', $data->description);
        $data2 = $response->api_callback->response->data[0];

        $info['no_awb']            = strval($data->barcode);
        $info['service']           = str_replace('Produk : ', '', $ket[10]);
        $info['tanggal_kirim']     = GlobalFunction::setDate($data->eventDate);
        $info['asal_pengiriman']   = ltrim($ket[3]);
        $info['tujuan_pengiriman'] = ltrim($ket[7]);
        $info['berat']             = ltrim(str_replace(['Berat :', 'gr'], '', $ket[11]));
        $info['catatan']           = str_replace('Isi Kiriman : ', '', $ket[13]);
        $info['nama_pengirim']     = str_replace('Pengirim : ', '', $ket[2]);
        $info['phone_pengirim']    = ltrim($ket[4]);
        $info['kota_pengirim']     = ltrim($ket[5]);
        $info['nama_penerima']     = ltrim(str_replace('Penerima : ', '', $ket[6]));
        $info['phone_penerima']    = ltrim($ket[8]);
        $info['kota_penerima']     = ltrim($ket[9]);

        $info['status_pengiriman'] = $data2->eventName;
        if ($data2->eventName == 'SELESAI ANTAR') {
            $info['tanggal_terima'] = GlobalFunction::setDate($data2->eventDate);
        }

        return (object) $info;
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

        foreach ($response->api_callback->response->data as $k => $v) {
            $desc                    = explode('~~', $v->description);
            $history[$k]['tanggal']  = GlobalFunction::setDate($v->eventDate);
            $history[$k]['posisi']   = ltrim($desc[1]);
            $history[$k]['message']  = $desc[0];

            if ($v->eventName == 'SELESAI ANTAR') {
                foreach ($desc as $key => $val) {
                    if (strpos($val, 'Diterima oleh') !== false) {
                        self::$yang_menerima = ltrim(str_replace('Diterima oleh', '', $val));
                    }
                }
            }
        }

        return array_reverse($history);
    }
}
