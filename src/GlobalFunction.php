<?php

namespace GTrack;

/**
 * Just Global Function
 */
class GlobalFunction
{

    public static function formatJntResponse($response)
    {
        $response       = json_decode($response);
        $res            = new \stdClass();
        $res->code      = $response->code;
        $res->data      = json_decode($response->data);
        $res->desc      = $response->desc;
        $res->success   = $response->success;

        return $res;
    }

    public static function randomStr($length = 10, $stringOnly = false, $smallStr = false)
    {
        if ($stringOnly) {
            $char = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        }else{
            $char = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        }

        if ($smallStr) {
            $char = '0123456789abcdefghijklmnopqrstuvwxyz';
        }

        $charLength = strlen($char);
        $randStr    = '';

        for ($i = 0; $i < $length; $i++) {
            $randStr .= $char[rand(0, $charLength - 1)];
        }
        return $randStr;
    }

}