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
class JneResponse
{
    public static $lastPossition;
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
        $data['eks']            = 'JNE';
        $data['site']           = 'https://www.jne.co.id';

        if ($isError) {
            $data['error']      = $isError;
            $data['message']    = self::$messageStatus;

            return json_decode(json_encode($data));
        }

        $cnote                  = $response->cnote;
        $detail                 = $response->detail[0];
        $data['error']          = $isError;
        $data['message']        = self::$messageStatus;
        $data['info']           = [
            'id'                => null,
            'no_awb'            => $cnote->cnote_no,
            'service'           => $cnote->cnote_services_code,
            'status'            => strtoupper($cnote->pod_status),
            'tanggal_kirim'     => GlobalFunction::setDate($cnote->cnote_date),
            'tanggal_terima'    => GlobalFunction::setDate($cnote->cnote_pod_date),
            'asal_pengiriman'   => $detail->cnote_origin,
            'tujuan_pengiriman' => $cnote->city_name,
            'harga'             => (int) $cnote->amount,
            'berat'             => (int) $cnote->weight * 1000, // gram
            'catatan'           => $cnote->goods_desc,
        ];
        $data['pengirim']       = [
            'nama'              => rtrim(strtoupper($detail->cnote_shipper_name)),
            'phone'             => null,
            'kota'              => rtrim($detail->cnote_shipper_city),
            'alamat1'           => Globalfunction::setIfNull($detail->cnote_shipper_addr1),
            'alamat2'           => Globalfunction::setIfNull($detail->cnote_shipper_addr2),
            'alamat3'           => Globalfunction::setIfNull($detail->cnote_shipper_addr3),
        ];
        $data['penerima']       = [
            'nama'              => rtrim(strtoupper($cnote->cnote_receiver_name)),
            'nama_penerima'     => Globalfunction::setIfNull($cnote->cnote_pod_receiver),
            'phone'             => null,
            'kota'              => rtrim($detail->cnote_receiver_city),
            'alamat1'           => Globalfunction::setIfNull($detail->cnote_receiver_addr1),
            'alamat2'           => Globalfunction::setIfNull($detail->cnote_receiver_addr2),
            'alamat3'           => Globalfunction::setIfNull($detail->cnote_receiver_addr3),
        ];
        $data['history']        = self::getHistory($response);

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
        if (isset($response->status) && !$response->status) {
            self::$messageStatus = $response->error;

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

        foreach ($response->history as $k => $v) {
            $history[$k]['tanggal'] = GlobalFunction::setDate($v->date);

            $pecah  = preg_split('/[\[\]]/', $v->desc);

            if (isset($pecah[1]) && !empty($pecah[1])) {
                self::setLastPossition(str_replace(' , ', ', ', $pecah[1]));
            }

            if (strpos($pecah[0], 'DELIVERED') !== false) {
                $explode                = explode(' | ', $pecah[1]);
                $history[$k]['posisi']  = rtrim(end($explode));
                $history[$k]['message'] = 'DELIVERED';
            } elseif (count($pecah) > 1) {
                $history[$k]['posisi']  = str_replace(' , ', ', ', $pecah[1]);
                $history[$k]['message'] = rtrim(str_replace(' AT', '', $pecah[0]));
            } else {
                $history[$k]['posisi']  = self::getLastPossition();
                $history[$k]['message'] = $pecah[0];
            }
        }

        return $history;
    }

    /**
     * Set posisi terakhir
     *
     * @param string $possition Posisi terakhir
     */
    private static function setLastPossition($possition)
    {
        self::$lastPossition = $possition;
    }

    /**
     * Get posisi terakhir
     */
    private static function getLastPossition()
    {
        return self::$lastPossition;
    }
}
