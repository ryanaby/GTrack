<?php
use GTrack\GTrack;

class ResponseTest extends \PHPUnit\Framework\TestCase
{

    private static $responseKey = [
        'eks',
        'site',
        'error',
        'message',
        'info' => [
            'id',
            'no_awb',
            'service',
            'status',
            'tanggal_kirim',
            'tanggal_terima',
            'asal_pengiriman',
            'tujuan_pengiriman',
            'harga',
            'berat',
            'catatan',
        ],
        'pengirim' => [
            'nama',
            'phone',
            'kota',
            'alamat1',
            'alamat2',
            'alamat3',
        ],
        'penerima' => [
            'nama',
            'nama_penerima',
            'phone',
            'kota',
            'alamat1',
            'alamat2',
            'alamat3',
        ],
        'history' => [
            [
                'tanggal',
                'posisi',
                'message'
            ]
        ]
    ];

    public function testJneResponse()
    {
        $GTrack   = new GTrack();
        $get      = $GTrack->jne('012030045457019');
        $response = json_decode(json_encode($get), true);

        foreach (static::$responseKey as $key => $val) {
            if (is_array($val)) {
                foreach ($val as $k => $v) {
                    if (is_array($v)) {
                        foreach ($v as $x => $y) {
                            $this->assertArrayHasKey($y, $response[$key][$x]);
                        }
                    }else{
                        $this->assertArrayHasKey($v, $response[$key]);
                    }
                }
            }else{
                $this->assertArrayHasKey($val, $response);
            }
        }
    }

    public function testJntResponse()
    {
        $GTrack   = new GTrack();
        $get      = $GTrack->jnt('JT6094602520');
        $response = json_decode(json_encode($get), true);

        foreach (static::$responseKey as $key => $val) {
            if (is_array($val)) {
                foreach ($val as $k => $v) {
                    if (is_array($v)) {
                        foreach ($v as $x => $y) {
                            $this->assertArrayHasKey($y, $response[$key][$x]);
                        }
                    }else{
                        $this->assertArrayHasKey($v, $response[$key]);
                    }
                }
            }else{
                $this->assertArrayHasKey($val, $response);
            }
        }
    }

    public function testTikiResponse()
    {
        $GTrack   = new GTrack();
        $get      = $GTrack->tiki('030125392642');
        $response = json_decode(json_encode($get), true);

        foreach (static::$responseKey as $key => $val) {
            if (is_array($val)) {
                foreach ($val as $k => $v) {
                    if (is_array($v)) {
                        foreach ($v as $x => $y) {
                            $this->assertArrayHasKey($y, $response[$key][$x]);
                        }
                    }else{
                        $this->assertArrayHasKey($v, $response[$key]);
                    }
                }
            }else{
                $this->assertArrayHasKey($val, $response);
            }
        }
    }

    public function testPosResponse()
    {
        $GTrack   = new GTrack();
        $get      = $GTrack->pos('16953453648');
        $response = json_decode(json_encode($get), true);

        foreach (static::$responseKey as $key => $val) {
            if (is_array($val)) {
                foreach ($val as $k => $v) {
                    if (is_array($v)) {
                        foreach ($v as $x => $y) {
                            $this->assertArrayHasKey($y, $response[$key][$x]);
                        }
                    }else{
                        $this->assertArrayHasKey($v, $response[$key]);
                    }
                }
            }else{
                $this->assertArrayHasKey($val, $response);
            }
        }
    }

    public function testWahanaResponse()
    {
        $GTrack   = new GTrack();
        $get      = $GTrack->wahana('APT65199');
        $response = json_decode(json_encode($get), true);

        foreach (static::$responseKey as $key => $val) {
            if (is_array($val)) {
                foreach ($val as $k => $v) {
                    if (is_array($v)) {
                        foreach ($v as $x => $y) {
                            $this->assertArrayHasKey($y, $response[$key][$x]);
                        }
                    }else{
                        $this->assertArrayHasKey($v, $response[$key]);
                    }
                }
            }else{
                $this->assertArrayHasKey($val, $response);
            }
        }
    }

    public function testSiCepatResponse()
    {
        $GTrack   = new GTrack();
        $get      = $GTrack->siCepat('000011779554');
        $response = json_decode(json_encode($get), true);

        foreach (static::$responseKey as $key => $val) {
            if (is_array($val)) {
                foreach ($val as $k => $v) {
                    if (is_array($v)) {
                        foreach ($v as $x => $y) {
                            $this->assertArrayHasKey($y, $response[$key][$x]);
                        }
                    }else{
                        $this->assertArrayHasKey($v, $response[$key]);
                    }
                }
            }else{
                $this->assertArrayHasKey($val, $response);
            }
        }
    }

    public function testNinjaXpressResponse()
    {
        $GTrack   = new GTrack();
        $get      = $GTrack->ninjaXpress('BLAPK191774574396');
        $response = json_decode(json_encode($get), true);

        foreach (static::$responseKey as $key => $val) {
            if (is_array($val)) {
                foreach ($val as $k => $v) {
                    if (is_array($v)) {
                        foreach ($v as $x => $y) {
                            $this->assertArrayHasKey($y, $response[$key][$x]);
                        }
                    }else{
                        $this->assertArrayHasKey($v, $response[$key]);
                    }
                }
            }else{
                $this->assertArrayHasKey($val, $response);
            }
        }
    }

    public function testJetExpressResponse()
    {
        $GTrack   = new GTrack();
        $get      = $GTrack->jetExpress('644031585256');
        $response = json_decode(json_encode($get), true);

        foreach (static::$responseKey as $key => $val) {
            if (is_array($val)) {
                foreach ($val as $k => $v) {
                    if (is_array($v)) {
                        foreach ($v as $x => $y) {
                            $this->assertArrayHasKey($y, $response[$key][$x]);
                        }
                    }else{
                        $this->assertArrayHasKey($v, $response[$key]);
                    }
                }
            }else{
                $this->assertArrayHasKey($val, $response);
            }
        }
    }

}
