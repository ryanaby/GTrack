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
class PosResponse extends Response
{
    /** @var array */
    public $ekspedisi  = [
        'name' => 'POS INDONESIA',
        'site' => 'posindonesia.co.id'
    ];

    /** @var string */
    private $namaPenerima;

    /** @var string */
    private $tanggalTerima;

    /** @var string */
    private $statusDelivery;

    /**
     * Format result yang diproses
     *
     * @param object $response response dari request
     *
     * @return object
     */
    public function result()
    {
        $response = reset($this->getResponse()->result); // Get first array
        $data     = explode(';', $response->description);
        $history  = $this->getHistory();

        return $this->build([
            'info'                  => [
                'no_awb'            => strval($response->barcode),
                'service'           => preg_replace('/(.*)LAYANAN :(.*)/', '$2', $data[0]),
                'status'            => $this->statusDelivery,
                'tanggal_kirim'     => Utils::setDate($response->eventDate),
                'tanggal_terima'    => $this->tanggalTerima,
                'asal_pengiriman'   => $data[4],
                'tujuan_pengiriman' => $data[10],
                'harga'             => null,
                'berat'             => null, // gram
                'catatan'           => null,
            ],
            'pengirim'              => [
                'nama'              => preg_replace('/(.*)PENGIRIM : (.*)/', '$2', $data[1]),
                'phone'             => $data[3],
                'kota'              => $data[4],
                'alamat'            => $data[2] . ', ' . $data[5],
            ],
            'penerima'              => [
                'nama'              => preg_replace('/(.*)PENERIMA : (.*)/', '$2', $data[7]),
                'nama_penerima'     => $this->namaPenerima,
                'phone'             => $data[9],
                'kota'              => $data[10],
                'alamat'            => $data[8] . ', ' . $data[11],
            ],
            'history'               => $history,
        ]);
    }

    /**
     * Check status, true if AWB is not found.
     *
     * @return bool
     */
    public function check()
    {
        if (isset($this->getResponse()->errors)) {
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

        foreach ($this->getResponse()->result as $k => $v) {
            $this->statusDelivery = $v->eventName;

            switch ($v->eventName) {
                case 'POSTING LOKET':
                    $history[$k] = [
                        'tanggal' => Utils::setDate($v->eventDate),
                        'posisi'  => $v->officeName,
                        'message' => 'Penerimaan di loket ' . $v->officeName,
                    ];
                    break;

                case 'MANIFEST SERAH':
                    $history[$k] = [
                        'tanggal' => Utils::setDate($v->eventDate),
                        'posisi'  => $v->officeName,
                        'message' => 'Diteruskan ke Hub ' . preg_replace('/(.*)KANTOR TUJUAN : (.*)/', '$2', $v->description),
                    ];
                    break;

                case 'MANIFEST TERIMA':
                    $history[$k] = [
                        'tanggal' => Utils::setDate($v->eventDate),
                        'posisi'  => $v->officeName,
                        'message' => 'Tiba di Hub ' . $v->officeName,
                    ];
                    break;

                case 'PROSES ANTAR':
                    $history[$k] = [
                        'tanggal' => Utils::setDate($v->eventDate),
                        'posisi'  => $v->officeName,
                        'message' => 'Proses antar di ' . $v->officeName,
                    ];
                    break;

                case 'SELESAI ANTAR':
                    if (Utils::exist('Antar Ulang', $v->description)) {
                        $message  = "Gagal antar di $v->officeName. ";
                        $message .= preg_replace('/(.*)KETERANGAN : (.*)/', '$2', $v->description);
                    } else {
                        $this->namaPenerima   = preg_replace('/(.*)PENERIMA \/ KETERANGAN : (.*)/', '$2', $v->description);
                        $this->tanggalTerima  = Utils::setDate($v->eventDate);
                        $this->statusDelivery = 'DELIVERED';

                        $message  = "Selesai antar di $v->officeName. ";
                        $message .= Utils::getBetween($v->description, 'STATUS: ', ';') . "($this->namaPenerima)";
                    }

                    $history[$k] = [
                        'tanggal' => Utils::setDate($v->eventDate),
                        'posisi'  => $v->officeName,
                        'message' => rtrim(preg_replace('!\s+!', ' ', $message)),
                    ];
                    break;

                default:
                    // code...
                    break;
            }
        }

        return $history;
    }
}
