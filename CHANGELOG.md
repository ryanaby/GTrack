# Change Log

## 2.0.1 - 2020-08-03

* Improvement: Set semua pengiriman selesai jadi "DELIVERED".
* Fix some bug.

## 2.0.0 - 2020-07-30

* Improvement: Fix setDate function into 24 hours format [#2](https://github.com/walangkaji/GTrack/pull/2).
* Remove `GTrack\CurlRequest`.
* Added `GTrack\Request` and `GTrack\Response` for handling request & response.
* Change `GTrack\GlobalFunction` to `GTrack\Utils\Utils`.
* Remove `$result->info->id`.
* Change `$result->eks` to `$result->name`.
* Change `$result->pengirim->alamat1`, `$result->pengirim->alamat2`, `$result->pengirim->alamat3` to `$result->pengirim->alamat`.
* Change `$result->penerima->alamat1`, `$result->penerima->alamat2`, `$result->penerima->alamat3` to `$result->penerima->alamat`.
* Added LION PARCEL [#1](https://github.com/walangkaji/GTrack/issues/1).
* Added ANTERAJA.
* Added REX KIRIMAN EXPRESS.

## 1.2.0 - 2020-04-18

* Support composer for installation.

## 1.1.0 - 2020-01-26

* Added setCookie request.
* Update POS API endpoint.
* Update TIKI API endpoint and change authorization.
* Added valid resi for test.
* Use php_cs style.

## 1.0.0 - 2019-03-13

* Initial