# GTrack API

GTrack merupakan sebuah repository yang digunakan untuk Tracking / Cek Resi pada beberapa ekspedisi pengiriman baik di kelas Indonesia maupun Internasional.

----------
### Cara Install

### 1. Via Clone Tepo
```sh
$ git clone https://github.com/walangkaji/GTrack.git
$ cd GTrack/
$ composer install
```

### 2. Via Donglot Tepo
1. Download zip [Disini](https://github.com/walangkaji/GTrack/archive/master.zip).
2. Extract.
3. Install requirements menggunakan composer:

```sh
$ composer install
```

### Saya belum mempunyai Composer

Bisa Install & Download [Disini](https://getcomposer.org/download/).

### Cara Pakai

```php
require 'vendor/autoload.php';

use GTrack\GTrack;

$GTrack = new GTrack();
$cek    = $GTrack->jne('011440046444019');

var_dump($cek);
```

### Ready digunakan

- **JNE**
- **J&T**
- **TIKI**
- **POS INDONESIA**
- **WAHANA**
- **SICEPAT**
- **NINJAXPRESS**
- **JET EXPRESS**
- **...**

### Methods
```php
GTrack::jne('xxxxxxx')
GTrack::jnt('xxxxxxx')
GTrack::tiki('xxxxxxx')
GTrack::pos('xxxxxxx')
GTrack::wahana('xxxxxxx')
GTrack::siCepat('xxxxxxx')
GTrack::ninjaXpress('xxxxxxx')
GTrack::jetExpress('xxxxxxx')
```

Cukup sekian dan Matursuwun.