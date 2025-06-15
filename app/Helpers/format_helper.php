<?php

if (!function_exists('nama_bulan_indo')) {
    function nama_bulan_indo($tanggal)
    {
        $bulan = date('m', strtotime($tanggal));
        $bulan_indo = [
            '01' => 'Januari', '02' => 'Februari', '03' => 'Maret',
            '04' => 'April', '05' => 'Mei', '06' => 'Juni',
            '07' => 'Juli', '08' => 'Agustus', '09' => 'September',
            '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
        ];
        return $bulan_indo[$bulan] ?? 'Bulan tidak diketahui';
    }
}
