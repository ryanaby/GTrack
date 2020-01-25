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
class JetExpressResponse
{
    public static $messageStatus;
    public static $status_delivery;
    public static $tanggal_kirim;
    public static $tanggal_terima;
    public static $asal_pengiriman;
    public static $nama_penerima;

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
        $data['eks']            = 'JET EXPRESS';
        $data['site']           = 'http://www.jetexpress.co.id';

        if ($isError) {
            $data['error']      = $isError;
            $data['message']    = self::$messageStatus;

            return json_decode(json_encode($data));
        }

        $responseData           = $response->data;
        $history                = self::getHistory($response);
        $data['error']          = $isError;
        $data['message']        = self::$messageStatus;
        $data['info']           = [
            'id'                => $responseData->id,
            'no_awb'            => $responseData->awbNumber,
            'service'           => $responseData->productName,
            'status'            => self::$status_delivery,
            'tanggal_kirim'     => self::$tanggal_kirim,
            'tanggal_terima'    => self::$tanggal_terima,
            'asal_pengiriman'   => self::$asal_pengiriman,
            'tujuan_pengiriman' => $responseData->displayDestinationCity,
            'harga'             => $responseData->totalFee,
            'berat'             => $responseData->totalWeight * 1000, // gram
            'catatan'           => null,
        ];
        $data['pengirim']       = [
            'nama'              => trim(strtoupper($responseData->shipperName)),
            'phone'             => null,
            'kota'              => self::$asal_pengiriman,
            'alamat1'           => self::$asal_pengiriman,
            'alamat2'           => null,
            'alamat3'           => null,
        ];
        $data['penerima']       = [
            'nama'              => strtoupper($responseData->consigneeName),
            'nama_penerima'     => self::$nama_penerima,
            'phone'             => null,
            'kota'              => $responseData->displayDestinationCity,
            'alamat1'           => $responseData->displayDestinationCity,
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
        if (empty($response->data)) {
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
        $gabung = [];

        // Gabung
        foreach ($response->history as $k => $v) {
            foreach ($v->tracks as $key => $value) {
                $gabung[] = $value;
            }
        }

        $gabung  = array_reverse($gabung);
        $history = [];

        foreach ($gabung as $k => $v) {
            self::$status_delivery = $v->status;

            if ($v->status == 'CREATED') {
                self::$tanggal_kirim   = GlobalFunction::setDate($v->trackDate);
                self::$asal_pengiriman = strtoupper($v->location);
            }

            if ($v->status == 'DELIVERED') {
                self::$tanggal_terima  = GlobalFunction::setDate($v->trackDate);
                self::$nama_penerima   = strtoupper($v->receiverName);
            }

            $history[$k]['tanggal']    = GlobalFunction::setDate($v->trackDate);
            $history[$k]['posisi']     = strtoupper($v->location);
            $history[$k]['message']    = ($v->operationStatusFail) ?
                                        trim($v->displayedStatus) . ' - ' . $v->operationStatusName :
                                        $v->displayedStatus;
        }

        return $history;
    }
}
