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
class JntResponse
{
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
        $response               = json_decode($response);
        $response               = json_decode($response->data)->bills[0];
        $details                = array_reverse($response->details);
        $isError                = self::isError($response);
        $data['eks']            = 'JNT';
        $data['site']           = 'http://www.jet.co.id';

        if ($isError) {
            $data['error']      = $isError;
            $data['message']    = self::$messageStatus;

            return json_decode(json_encode($data));
        }

        $detail                 = self::getDetail($details);
        $data['error']          = $isError;
        $data['message']        = self::$messageStatus;
        $data['info']           = [
            'id'                => null,
            'no_awb'            => $response->billCode,
            'service'           => null,
            'status'            => strtoupper($response->status),
            'tanggal_kirim'     => GlobalFunction::setDate($details[0]->acceptTime),
            'tanggal_terima'    => $detail['tanggal_terima'],
            'asal_pengiriman'   => $details[0]->city,
            'tujuan_pengiriman' => $detail['tujuan_pengiriman'],
            'harga'             => null,
            'berat'             => null, // gram
            'catatan'           => null,
        ];

        $data['pengirim']       = [
            'nama'              => $detail['nama_pengirim'],
            'phone'             => $detail['phone_pengirim'],
            'kota'              => $details[0]->city,
            'alamat1'           => $details[0]->city,
            'alamat2'           => null,
            'alamat3'           => null,
        ];
        $data['penerima']       = [
            'nama'              => $detail['tujuan_pengiriman'],
            'nama_penerima'     => $detail['nama_penerima'],
            'phone'             => null,
            'kota'              => $detail['kota_penerima'],
            'alamat1'           => $detail['kota_penerima'],
            'alamat2'           => null,
            'alamat3'           => null,
        ];
        $data['history']        = self::getHistory($details);

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
        if (empty($response->details)) {
            self::$messageStatus = 'No AWB tidak ditemukan.';

            return true;
        } else {
            self::$messageStatus = 'success';

            return false;
        }
    }

    /**
     * Get detail pengiriman
     *
     * @param array $details array dari history jnt
     *
     * @return array
     */
    private static function getDetail($details)
    {
        $result                      = [];
        $result['tanggal_terima']    = null;
        $result['tujuan_pengiriman'] = null;
        $result['nama_penerima']     = null;
        $result['kota_penerima']     = null;
        $result['nama_pengirim']     = null;
        $result['phone_pengirim']    = null;

        foreach ($details as $key) {

            // Barang terkirim ke tujuan
            if ($key->scanstatus == 'Terkirim') {
                $result['tanggal_terima']    = GlobalFunction::setDate($key->acceptTime);
                $result['tujuan_pengiriman'] = strtoupper($key->city);
                $result['nama_penerima']     = strtoupper($key->signer);
                $result['kota_penerima']     = strtoupper($key->city);
            }
        }

        return $result;
    }

    /**
     * Compile history dengan format yang sudah disesuaikan
     *
     * @param array $details detail history
     *
     * @return array
     */
    private static function getHistory($details)
    {
        $history = [];

        foreach ($details as $k => $v) {
            $history[$k]['tanggal'] = GlobalFunction::setDate($v->acceptTime);
            $history[$k]['posisi']  = $v->city . ' (' . $v->siteName . ')';

            switch ($v->scanstatus) {
                case 'Telah Berangkat':
                    $history[$k]['message'] = $v->scanstatus . ' dari ' . $v->state . ' menuju ' . $v->nextsite;
                    break;
                case 'Telah Diambil':
                    $history[$k]['message'] = $v->scanstatus . ' oleh ' . strtoupper($v->deliveryName) . ' (' . $v->deliveryPhone . ') dari ' . $v->state;
                    break;
                case 'Telah Tiba':
                    $history[$k]['message'] = $v->scanstatus . ' ke ' . $v->state;
                    break;
                case 'Sedang Diantar':
                    $history[$k]['message'] = $v->scanstatus . ' oleh ' . strtoupper($v->deliveryName) . ' (' . $v->deliveryPhone . ') dari ' . $v->state;
                    break;
                case 'Terkirim':
                    $history[$k]['message'] = $v->scanstatus . ' kepada ' . strtoupper($v->signer);
                    break;
            }
        }

        return $history;
    }
}
