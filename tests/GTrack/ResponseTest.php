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

    public function getValidResi($name)
    {
        $json = file_get_contents('test_valid_resi.json');
        $data = json_decode($json, true);

        return $data[$name];
    }

    public function testJneResponse()
    {
        $GTrack   = new GTrack();
        $get      = $GTrack->jne($this->getValidResi('jne'));
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
        $get      = $GTrack->jnt($this->getValidResi('jnt'));
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
        $get      = $GTrack->tiki($this->getValidResi('tiki'));
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
        $get      = $GTrack->pos($this->getValidResi('pos'));
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
        $get      = $GTrack->wahana($this->getValidResi('wahana'));
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
        $get      = $GTrack->siCepat($this->getValidResi('siCepat'));
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
        $get      = $GTrack->ninjaXpress($this->getValidResi('ninjaXpress'));
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
        $get      = $GTrack->jetExpress($this->getValidResi('jetExpress'));
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
