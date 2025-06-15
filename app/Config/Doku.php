<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Doku extends BaseConfig
{
    public $mall_id = 'YOUR_MALL_ID';
    public $shared_key = 'YOUR_SHARED_KEY';
    public $words_salt = 'YOUR_WORDS_SALT'; // Jika ada
    public $payment_url = 'https://sandbox.doku.com/Suite/Receive';
    public $return_url = 'https://yourdomain.com/payment/return';
    public $notify_url = 'https://yourdomain.com/payment/notify';
}
