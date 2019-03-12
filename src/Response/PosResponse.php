<?php
namespace GTrack\Response;
use \GTrack\GlobalFunction;

/**
 * Formatting response
 */
class PosResponse
{

    public static $messageStatus;
    public static $tanggal_kirim;

    /**
     * Format result yang diproses
     * 
     * @param  object $response response dari request
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
            'status'            => strtoupper($info->status_delivery),
            'tanggal_kirim'     => self::$tanggal_kirim,
            'tanggal_terima'    => GlobalFunction::setDate($info->diterima_tanggal),
            'asal_pengiriman'   => $info->alamat_pengirim,
            'tujuan_pengiriman' => $info->alamat_penerima,
            'harga'             => null,
            'berat'             => null, // gram
            'catatan'           => null,
        ];
        $data['pengirim']       = [
            'nama'              => strtoupper($info->nama_pengirim),
            'phone'             => null,
            'kota'              => null,
            'alamat1'           => $info->alamat_pengirim,
            'alamat2'           => null,
            'alamat3'           => null,
        ];
        $data['penerima']       = [
            'nama'              => strtoupper($info->nama_penerima),
            'nama_penerima'     => strtoupper($info->diterima_oleh),
            'phone'             => null,
            'kota'              => null,
            'alamat1'           => $info->alamat_penerima,
            'alamat2'           => null,
            'alamat3'           => null,
        ];
        $data['history']        = $history;

        return json_decode(json_encode($data));
    }

    /**
     * Get status dan message
     * 
     * @param  object $response
     */
    private static function isError($response)
    {
        if (!empty($response->error)) {
            self::$messageStatus = $response->error;
            return true;
        }else{
            self::$messageStatus = 'success';
            return false;
        }
    }

    /**
     * Get status dan message
     * 
     * @param  object $response
     */
    private static function getInfo($response)
    {
        $result = [];
        $info = $response->res->det;
        foreach ($info as $key) {
            switch ($key->k) {
                case 'No Resi':
                    $result['no_awb'] = $key->v;
                    break;
                case 'Status':
                    $result['status_delivery'] = $key->v;
                    break;
                case 'Layanan':
                    $result['service'] = $key->v;
                    break;
                case 'Pengirim':
                    $result['nama_pengirim'] = $key->v;
                    break;
                case 'Alamat Pengirim':
                    $result['alamat_pengirim'] = $key->v;
                    break;
                case 'Penerima':
                    $result['nama_penerima'] = $key->v;
                    break;
                case 'Alamat Penerima':
                    $result['alamat_penerima'] = $key->v;
                    break;
                case 'Diterima Tgl':
                    $result['diterima_tanggal'] = $key->v;
                    break;
                case 'Diterima Oleh':
                    $result['diterima_oleh'] = $key->v;
                    break;
            }
        }

        return (object)$result;
    }

    /**
     * Compile history dengan format yang sudah disesuaikan
     * 
     * @param  object $response
     */
    private static function getHistory($response)
    {
        $history = [];

        foreach ($response->res->trc as $k => $v) {
            $date = GlobalFunction::setDate($v->tgl . ' ' . $v->bln . ' 2019 ' . $v->jam);

            if ($k == 0) {
                self::$tanggal_kirim = $date;
            }

            $history[$k]['tanggal']  = $date;
            $history[$k]['posisi']   = $v->stat;
            $history[$k]['message']  = $v->ket;
        }

        return $history;
    }

}