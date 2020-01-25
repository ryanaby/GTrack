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
class WahanaResponse
{
    public static $messageStatus;
    public static $tanggal_terima;
    public static $nama_penerima;
    public static $asal_pengiriman;

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
        $data['eks']            = 'WAHANA';
        $data['site']           = 'https://wahana.com';

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
            'no_awb'            => $response->TTKNO,
            'service'           => null,
            'status'            => self::getStatusDelivery($response),
            'tanggal_kirim'     => GlobalFunction::setDate($response->data[0]->Tanggal),
            'tanggal_terima'    => GlobalFunction::setDate(self::$tanggal_terima),
            'asal_pengiriman'   => strtoupper(self::$asal_pengiriman),
            'tujuan_pengiriman' => strtoupper($response->Alamatpenerima),
            'harga'             => null,
            'berat'             => null, // gram
            'catatan'           => null,
        ];
        $data['pengirim']       = [
            'nama'              => trim(strtoupper(preg_replace('/\s+/', ' ', $response->Pengirim))),
            'phone'             => null,
            'kota'              => strtoupper(self::$asal_pengiriman),
            'alamat1'           => strtoupper(self::$asal_pengiriman),
            'alamat2'           => null,
            'alamat3'           => null,
        ];
        $data['penerima']       = [
            'nama'              => trim(strtoupper(preg_replace('/\s+/', ' ', $response->Penerima))),
            'nama_penerima'     => trim(strtoupper(self::$nama_penerima)),
            'phone'             => $response->NOTELP,
            'kota'              => strtoupper($response->Alamatpenerima),
            'alamat1'           => strtoupper($response->Alamatpenerima),
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
        if ($response->status == 'error') {
            self::$messageStatus = $response->error;

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
        if ($response->StatusTerakhir == 3) {
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

        foreach ($response->data as $k => $v) {
            $history[$k]['tanggal'] = GlobalFunction::setDate($v->Tanggal);

            switch ($v->StatusInternal) {
                case 'Baru':
                    self::$asal_pengiriman = preg_replace('/Diterima di Sales Counter AGEN WPL (.*)/', '$1', $v->TrackStatusNama);
                    $history[$k]['posisi'] = preg_replace('/Diterima di Sales Counter (.*)/', '$1', $v->TrackStatusNama);
                    break;

                case 'Manifest Pickup':
                    $history[$k]['posisi'] = preg_replace('/Di pickup oleh petugas (.*)/', '$1', $v->TrackStatusNama);
                    break;

                case 'Serah Terima Pickup':
                    $history[$k]['posisi'] = preg_replace('/Diterima di fasilitas (.*)/', '$1', $v->TrackStatusNama);
                    break;

                case 'Moda Angkutan':
                    $history[$k]['posisi'] = preg_replace('/Pengiriman dari (.*) ke (.*)/', '$1', $v->TrackStatusNama);
                    break;

                case 'Serah Terima Surat Muatan':
                    $history[$k]['posisi'] = preg_replace('/Diterima di fasilitas (.*)/', '$1', $v->TrackStatusNama);
                    break;

                case 'Serah Terima Manifest':
                    $history[$k]['posisi'] = preg_replace('/Diterima di fasilitas (.*)/', '$1', $v->TrackStatusNama);
                    break;

                case 'Surat Jalan Kurir':
                    $history[$k]['posisi'] = preg_replace('/Proses pengantaran oleh kurir (.*), (.*)/', '$1', $v->TrackStatusNama);
                    break;

                case 'Terkirim/Diterima':
                    $history[$k]['posisi'] = 'Diterima';
                    self::$tanggal_terima  = $v->Tanggal;
                    self::$nama_penerima   = preg_replace('/Diterima oleh (.*)\((.*)/', '$1', $v->TrackStatusNama);
                    break;

                default:
                    $history[$k]['posisi'] = null;
                    break;
            }

            $history[$k]['message'] = $v->TrackStatusNama;
        }

        return $history;
    }
}
