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
class SiCepatResponse
{
    public static $messageStatus;
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
        $data['eks']            = 'SICEPAT';
        $data['site']           = 'http://sicepat.com';

        if ($isError) {
            $data['error']      = $isError;
            $data['message']    = self::$messageStatus;

            return json_decode(json_encode($data));
        }

        $history                = self::getHistory($response);
        $data['error']          = $isError;
        $data['message']        = self::$messageStatus;
        $data['info']           = [
            'id'                => null,
            'no_awb'            => $response->result->waybill_number,
            'service'           => $response->result->service,
            'status'            => self::getStatusDelivery($response),
            'tanggal_kirim'     => GlobalFunction::setDate($response->result->send_date),
            'tanggal_terima'    => GlobalFunction::setDate($response->result->POD_receiver_time),
            'asal_pengiriman'   => strtoupper($response->result->sender_address),
            'tujuan_pengiriman' => strtoupper($response->result->receiver_address),
            'harga'             => null,
            'berat'             => $response->result->weight * 1000, // gram
            'catatan'           => null,
        ];
        $data['pengirim']       = [
            'nama'              => trim(strtoupper($response->result->sender)),
            'phone'             => null,
            'kota'              => strtoupper($response->result->sender_address),
            'alamat1'           => strtoupper($response->result->sender_address),
            'alamat2'           => null,
            'alamat3'           => null,
        ];
        $data['penerima']       = [
            'nama'              => trim(strtoupper($response->result->receiver_name)),
            'nama_penerima'     => trim(strtoupper(self::$nama_penerima)),
            'phone'             => null,
            'kota'              => strtoupper($response->result->receiver_address),
            'alamat1'           => strtoupper($response->result->receiver_address),
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
        if ($response->status->code == 400) {
            self::$messageStatus = $response->status->description;

            return true;
        } else {
            self::$messageStatus = 'success';

            return false;
        }
    }

    /**
     * Get status pengiriman
     *
     * @param object $response response dari request
     *
     * @return string
     */
    public static function getStatusDelivery($response)
    {
        if ($response->result->last_status->status == 'DELIVERED') {
            self::$nama_penerima = preg_replace('/(.*)\[(.*) - (.*)\](.*)/', '$2', $response->result->last_status->receiver_name);

            return 'DELIVERED';
        } else {
            return 'ON PROCESS';
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

        foreach ($response->result->track_history as $k => $v) {
            if ($v->status == 'DELIVERED') {
                $history[$k]['posisi']     = 'Diterima';
                $history[$k]['message']    = $v->receiver_name;
            } else {
                $history[$k]['tanggal']    = GlobalFunction::setDate($v->date_time);
                $history[$k]['posisi']     = preg_replace('/(.*)\[(.*)\](.*)/', '$2', $v->city);

                if (strpos($v->city, 'SIGESIT') !== false) {
                    $history[$k]['posisi'] = 'Diantar';
                }

                $history[$k]['message']    = $v->city;
            }
        }

        return $history;
    }
}
