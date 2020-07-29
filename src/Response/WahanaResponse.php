<?php
/**
 * This file is part of GTrack.
 *
 * @author walangkaji <walangkaji@outlook.com>
 */

namespace GTrack\Response;

use GTrack\Response;
use GTrack\Utils\Utils;

/**
 * Formatting response
 */
class WahanaResponse extends Response
{
    /** @var array */
    public $ekspedisi  = [
        'name' => 'WAHANA',
        'site' => 'wahana.com'
    ];

    /** @var string */
    private $tanggalTerima;

    /** @var string */
    private $namaPenerima;

    /** @var string */
    private $asalPengiriman;

    /**
     * Format result yang diproses
     *
     * @return object
     */
    public function result()
    {
        $history = $this->getHistory();

        return $this->build([
            'info'                  => [
                'no_awb'            => $this->response->TTKNO,
                'service'           => null,
                'status'            => $this->response->StatusTerakhir == 3 ? 'DELIVERED' : 'ON PROCESS',
                'tanggal_kirim'     => Utils::setDate(reset($this->response->data)->Tanggal),
                'tanggal_terima'    => $this->tanggalTerima,
                'asal_pengiriman'   => $this->asalPengiriman,
                'tujuan_pengiriman' => $this->response->Alamatpenerima,
                'harga'             => null,
                'berat'             => null, // gram
                'catatan'           => null,
            ],
            'pengirim'              => [
                'nama'              => trim(strtoupper(preg_replace('/\s+/', ' ', $this->response->Pengirim))),
                'phone'             => null,
                'kota'              => $this->asalPengiriman,
                'alamat'            => $this->asalPengiriman,
            ],
            'penerima'              => [
                'nama'              => trim(strtoupper(preg_replace('/\s+/', ' ', $this->response->Penerima))),
                'nama_penerima'     => rtrim($this->namaPenerima),
                'phone'             => $this->response->NOTELP,
                'kota'              => $this->response->Alamatpenerima,
                'alamat'            => $this->response->Alamatpenerima,
            ],
            'history'               => $history
        ]);
    }

    /**
     * Check status, true if AWB is not found.
     *
     * @return bool
     */
    public function check()
    {
        if ($this->getResponse()->status == 'error') {
            return true;
        }

        return false;
    }

    /**
     * Compile history dengan format yang sudah disesuaikan
     *
     * @return array
     */
    private function getHistory()
    {
        $history = [];

        foreach ($this->getResponse()->data as $k => $v) {
            $history[$k]['tanggal'] = Utils::setDate($v->Tanggal);

            switch ($v->StatusInternal) {
                case 'Baru':
                    $this->asalPengiriman  = preg_replace('/Diterima di Sales Counter AGEN WPL (.*)/', '$1', $v->TrackStatusNama);
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
                    $this->tanggalTerima   = $v->Tanggal;
                    $this->namaPenerima    = preg_replace('/Diterima oleh (.*)\((.*)/', '$1', $v->TrackStatusNama);
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
