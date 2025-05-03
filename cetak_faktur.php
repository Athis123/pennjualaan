<?php
// Hentikan spasi kosong/output sebelum header
ob_start();

require 'vendor/autoload.php'; // Pastikan path ini benar

use Dompdf\Dompdf;

// Koneksi database
$koneksi = new mysqli("localhost", "root", "", "penjualan");

// Ambil ID penjualan
$id_penjualan = intval($_GET['id'] ?? 0);

// Cek data penjualan
$penjualan = $koneksi->query("
    SELECT penjualan.*, customer.nama AS nama_customer, customer.telepon, customer.alamat
    FROM penjualan
    JOIN customer ON penjualan.id_customer = customer.id
    WHERE penjualan.id = $id_penjualan
")->fetch_assoc();


if (!$penjualan) {
    die('Data penjualan tidak ditemukan');
}

// Ambil detail produk
$detail = $koneksi->query("
    SELECT detail_penjualan.*, produk.nama AS nama_produk
    FROM detail_penjualan
    JOIN produk ON detail_penjualan.id_produk = produk.id
    WHERE detail_penjualan.id_penjualan = $id_penjualan
");

// Buat HTML
$html = '
<style>
    body { font-family: sans-serif; font-size: 11px; }
    table { width: 100%; border-collapse: collapse; }
    th, td { border: 1px solid #000; padding: 5px; }
    .no-border td { border: none; }
    .center { text-align: center; }
    .right { text-align: right; }
    .left { text-align: left; }
</style>

<h2 class="center">Faktur Penjualan</h2>

<table class="no-border" style="margin-bottom: 15px;">
    <tr>
        <td class="left" width="50%"><strong>Nama Customer:</strong> ' . htmlspecialchars($penjualan['nama_customer']) . '</td>
        <td class="left"><strong>Tanggal:</strong> 28 April 2025</td>

    </tr>
    <tr>
        <td class="left"><strong>No. Tlp:</strong> ' . htmlspecialchars($penjualan['telepon']) . '</td>
        <td class="left"><strong>No Faktur:</strong> ' . htmlspecialchars($penjualan['no_faktur'] ?? '021001') . '</td>
    </tr>
    <tr>
        <td class="left"><strong>Alamat:</strong> ' . htmlspecialchars($penjualan['alamat']) . '</td>
        <td class="left"><strong>Pembayaran:</strong> ' . htmlspecialchars($penjualan['pembayaran'] ?? 'Tunai') . '</td>
    </tr>
</table>

<table>
    <thead>
        <tr class="center">
            <th width="5%">No</th>
            <th width="35%">Nama Barang</th>
            <th width="10%">Qty</th>
            <th width="15%">Harga</th>
            <th width="10%">Disc</th>
            <th width="25%">Subtotal</th>
        </tr>
    </thead>
    <tbody>';

$no = 1;
$total = 0;
while ($row = $detail->fetch_assoc()) {
    $subtotal = $row['subtotal'];
    $total += $subtotal;
    $html .= '
        <tr>
            <td class="center">' . $no++ . '</td>
            <td class="left">' . htmlspecialchars($row['nama_produk']) . '</td>
            <td class="center">' . $row['jumlah'] . '</td>
            <td class="right">' . number_format($row['harga'], 0, ',', '.') . '</td>
            <td class="center">5%</td>
            <td class="right">' . number_format($subtotal, 0, ',', '.') . '</td>
        </tr>';
}

$html .= '
    </tbody>
</table>';

// Hitung diskon, pajak, grand total
$diskon_persen = 5;
$diskon_rupiah = ($total * $diskon_persen) / 100;
$total_setelah_diskon = $total - $diskon_rupiah;

$pajak_persen = 11; // PPN 11%
$pajak_rupiah = ($total_setelah_diskon * $pajak_persen) / 100;

$grand_total = $total_setelah_diskon + $pajak_rupiah;

// Bagian total
$html .= '
<table class="no-border" style="margin-top: 15px;">
    <tr>
        <td class="right" width="85%"><strong>Total :</strong></td>
        <td class="right"><strong>' . number_format($total, 0, ',', '.') . '</strong></td>
    </tr>
    <tr>
        <td class="right"><strong>Diskon (' . $diskon_persen . '%) :</strong></td>
        <td class="right">-' . number_format($diskon_rupiah, 0, ',', '.') . '</td>
    </tr>
    <tr>
        <td class="right"><strong>Pajak (' . $pajak_persen . '%) :</strong></td>
        <td class="right">' . number_format($pajak_rupiah, 0, ',', '.') . '</td>
    </tr>
    <tr>
        <td class="right"><strong>Grand Total :</strong></td>
        <td class="right"><strong>' . number_format($grand_total, 0, ',', '.') . '</strong></td>
    </tr>
</table>';

// Buat PDF
use Dompdf\Options;

// Optional: Setting Dompdf supaya support HTML5 dan UTF-8 lebih baik
$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isRemoteEnabled', true); // Kalau ada gambar dari URL

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);

// Setting ukuran dan orientasi kertas
$dompdf->setPaper('A4', 'portrait');

// Render
$dompdf->render();

// Bersihkan output buffer dan tampilkan PDF
ob_end_clean();
$dompdf->stream('faktur_penjualan_' . $penjualan['id'] . '.pdf', ['Attachment' => false]);
exit;
?>
