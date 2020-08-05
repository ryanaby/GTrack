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
    private $kotaPengirim;

    /**
     * Format result yang diproses
     *
     * @return object
     */
    public function result()
    {
        $history = $this->getHistory();

        return $this->build([
            'info'               => [
                'no_awb'         => $this->response->TTKNO,
                'service'        => null,
                'status'         => $this->response->StatusTerakhir == 3 ? 'DELIVERED' : 'ON PROCESS',
                'tanggal_kirim'  => Utils::setDate(reset($this->response->data)->Tanggal),
                'tanggal_terima' => $this->tanggalTerima,
                'harga'          => null,
                'berat'          => null, // gram
                'catatan'        => null,
            ],
            'pengirim'           => [
                'nama'           => trim(strtoupper(preg_replace('/\s+/', ' ', $this->response->Pengirim))),
                'phone'          => null,
                'alamat'         => $this->kotaPengirim,
            ],
            'penerima'           => [
                'nama'           => trim(strtoupper(preg_replace('/\s+/', ' ', $this->response->Penerima))),
                'nama_penerima'  => rtrim($this->namaPenerima),
                'phone'          => $this->response->NOTELP,
                'alamat'         => strtoupper($this->response->Alamatpenerima),
            ],
            'history'            => $history
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
                    $history[$k]['posisi'] = preg_replace('/Diterima di Sales Counter (.*)/', '$1', $v->TrackStatusNama);
                    break;

                case 'Manifest Pickup':
                    $posisi                = preg_replace('/Di pickup oleh petugas (.*)/', '$1', $v->TrackStatusNama);
                    $history[$k]['posisi'] = $posisi;
                    $this->kotaPengirim    = strtoupper($posisi);
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
