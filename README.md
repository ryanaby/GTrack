# GTrack API | PHP Cek Resi
<p align="center">
	<img src="https://raw.githubusercontent.com/walangkaji/emboh/master/img/GTrack.png" />
</p>

GTrack merupakan sebuah repository yang digunakan untuk Tracking / Cek Resi pada beberapa ekspedisi pengiriman baik di Indonesia maupun Internasional.
- Cara penggunaanya yang cukup simple.
- Response yang sudah konsisten untuk semua ekspedisi.

----------
### Install GTrack

Rekomendasinya dengan menggunakan [Composer](https://getcomposer.org/).

```bash
$ composer require walangkaji/gtrack
```

atau bisa juga dengan cara clone

```bash
$ git clone https://github.com/walangkaji/GTrack.git
$ cd GTrack/
$ composer install
```

### Cara Pakai

```php
require 'vendor/autoload.php';

use GTrack\GTrack;

$GTrack = new GTrack();
$cek    = $GTrack->jne('011440046444019');

var_dump($cek);
```

Apabila pengen menggunakan proxy:
```php
$proxy  = '192.168.1.1:1111';
$GTrack = new GTrack($proxy);
```

### Supported

- **JNE**
- **J&T**
- **TIKI**
- **POS INDONESIA**
- **WAHANA**
- **SICEPAT**
- **NINJAXPRESS**
- **JET EXPRESS**
- **LION PARCEL**
- **ANTERAJA**
- **REX KIRIMAN EXPRESS**
- **...**

### Methods
```php
$GTrack->jne('xxxxxxx');
$GTrack->jnt('xxxxxxx');
$GTrack->tiki('xxxxxxx');
$GTrack->pos('xxxxxxx');
$GTrack->wahana('xxxxxxx');
$GTrack->siCepat('xxxxxxx');
$GTrack->ninjaXpress('xxxxxxx');
$GTrack->jetExpress('xxxxxxx');
$GTrack->lionParcel('xxxxxxx');
$GTrack->anterAja('xxxxxxx');
$GTrack->rex('xxxxxxx');
```

Cukup sekian dan Matursuwun.

Jangan lupa kalo mau support seikhlasnya bisa lewat sini:
- ![Paypal](https://raw.githubusercontent.com/walangkaji/emboh/master/img/paypal.png) Paypal: [Se-Ikhlasnya Saja](https://www.paypal.me/walangkaji)
