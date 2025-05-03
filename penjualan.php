<?php
$koneksi = new mysqli("localhost", "root", "", "penjualan");

// Ambil daftar produk dan customer
$produk = $koneksi->query("SELECT * FROM produk");
$customer = $koneksi->query("SELECT * FROM customer");

// Proses tambah penjualan
if (isset($_POST['tambah_penjualan'])) {
    $id_customer = $_POST['id_customer'];
    $total = $_POST['total'];

    $koneksi->query("INSERT INTO penjualan (id_customer, total) VALUES ('$id_customer', '$total')");
    $id_penjualan = $koneksi->insert_id;

    foreach ($_POST['produk_id'] as $index => $id_produk) {
        $jumlah = $_POST['jumlah'][$index];
        $harga = $_POST['harga'][$index];
        $subtotal = $jumlah * $harga;

        $koneksi->query("INSERT INTO detail_penjualan (id_penjualan, id_produk, jumlah, harga, subtotal) 
                         VALUES ('$id_penjualan', '$id_produk', '$jumlah', '$harga', '$subtotal')");
    }

    header("Location: penjualan.php?success=true");
    exit;
}

// Proses hapus penjualan
if (isset($_GET['hapus'])) {
    $id = intval($_GET['hapus']);
    $koneksi->query("DELETE FROM detail_penjualan WHERE id_penjualan = $id");
    $koneksi->query("DELETE FROM penjualan WHERE id = $id");
    header("Location: penjualan.php?deleted=true");
    exit;
}

// Proses edit penjualan
if (isset($_POST['edit_penjualan'])) {
    $id_penjualan = $_POST['id_penjualan'];
    $id_customer = $_POST['id_customer'];
    $total = $_POST['total'];

    $koneksi->query("UPDATE penjualan SET id_customer='$id_customer', total='$total' WHERE id='$id_penjualan'");
    $koneksi->query("DELETE FROM detail_penjualan WHERE id_penjualan='$id_penjualan'");

    foreach ($_POST['produk_id'] as $index => $id_produk) {
        $jumlah = $_POST['jumlah'][$index];
        $harga = $_POST['harga'][$index];
        $subtotal = $jumlah * $harga;

        $koneksi->query("INSERT INTO detail_penjualan (id_penjualan, id_produk, jumlah, harga, subtotal) 
                         VALUES ('$id_penjualan', '$id_produk', '$jumlah', '$harga', '$subtotal')");
    }

    header("Location: penjualan.php?updated=true");
    exit;
}

// Tampilkan data penjualan
$data_penjualan = $koneksi->query("
    SELECT penjualan.*, customer.nama AS nama_customer
    FROM penjualan
    JOIN customer ON penjualan.id_customer = customer.id
    ORDER BY penjualan.id DESC
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaksi Penjualan</title>
    <link href="asset/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link href="asset/css/sb-admin-2.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body id="page-top">
<div id="wrapper">

    <!-- Sidebar -->
    <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.php">
                <div class="sidebar-brand-text mx-3">Muhammad Kahfi</div>
            </a>
            <li class="nav-item">
                <a class="nav-link" href="index.html">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span></a>
            </li>
            <!-- Nav Item - perusahaan -->
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseTwo"
                    aria-expanded="true" aria-controls="collapseTwo">
                    <i class="fas fa-fw fa-cog"></i>
                    <span>Master</span>
                </a>
                <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">Master Data</h6>
                        <a class="collapse-item" href="perusahaan.php" id="m_perusahaan">Perusahaan</a>
                        <a class="collapse-item" href="customer.php" id="m_perusahaan">Customer</a>
                        <a class="collapse-item" href="produk.php" id="m_penjualan">Produk</a>
                    </div>
                </div>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="penjualan.php">
                    <i class="fas fa-fw fa-table"></i>
                    <span>Penjualan</span></a>
            </li>
        </ul>
        <!-- End of Sidebar -->

    <!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column">
        <div id="content">

            <!-- Begin Page Content -->
            <div class="container-fluid">

                <h1 class="h3 mb-4 text-gray-800">Transaksi Penjualan</h1>

                <!-- Form Tambah/Edit Penjualan -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Tambah / Edit Penjualan</h6>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <input type="hidden" name="id_penjualan" id="id_penjualan">
                            
                            <div class="mb-3">
                                <label class="form-label">Pilih Customer</label>
                                <select name="id_customer" id="id_customer" class="form-select" required>
                                    <?php 
                                    $customer->data_seek(0);
                                    while ($row = $customer->fetch_assoc()): ?>
                                        <option value="<?= $row['id'] ?>"><?= $row['nama'] ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>

                            <div id="produk-container">
                                <div class="mb-3 produk-item">
                                    <label class="form-label">Pilih Produk</label>
                                    <select name="produk_id[]" class="form-select produk-id" required>
                                        <option value="">-- Pilih Produk --</option>
                                        <?php 
                                        $produk->data_seek(0);
                                        while ($row = $produk->fetch_assoc()): ?>
                                            <option value="<?= $row['id'] ?>" data-harga="<?= $row['harga'] ?>">
                                                <?= $row['nama'] ?> - <?= number_format($row['harga']) ?> IDR
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                    <label class="form-label">Harga</label>
                                    <input type="number" name="harga[]" class="form-control harga" readonly required>
                                    <label class="form-label">Jumlah</label>
                                    <input type="number" name="jumlah[]" class="form-control jumlah" min="1" required>
                                </div>
                            </div>

                            <button type="button" id="add-produk" class="btn btn-secondary mb-3">Tambah Produk</button>

                            <div class="mb-3">
                                <label class="form-label">Total</label>
                                <input type="number" name="total" class="form-control" id="total" readonly required>
                            </div>

                            <button type="submit" name="tambah_penjualan" class="btn btn-primary">Selesaikan Transaksi</button>
                        </form>
                    </div>
                </div>

                <!-- Table Penjualan -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Data Penjualan</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Customer</th>
                                        <th>Total</th>
                                        <th>Tanggal</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $no = 1; while($row = $data_penjualan->fetch_assoc()): ?>
                                        <tr>
                                            <td><?= $no++ ?></td>
                                            <td><?= $row['nama_customer'] ?></td>
                                            <td><?= number_format($row['total']) ?> IDR</td>
                                            <td><?= $row['created_at'] ?? date('Y-m-d') ?></td>
                                            <td>
                                            <a href="cetak_faktur.php?id=<?= $row['id'] ?>" class="btn btn-success btn-sm" target="_blank">
                                                <i class="fas fa-file-pdf"></i> Export PDF
                                            </a>
                                                <a href="penjualan.php?edit=<?= $row['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                                                <a href="penjualan.php?hapus=<?= $row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus?')">Delete</a>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>

</div>
    <!-- Bootstrap core JavaScript -->
    <script src="asset/vendor/jquery/jquery.min.js"></script>
    <script src="asset/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript -->
    <script src="asset/vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages -->
    <script src="asset/js/sb-admin-2.min.js"></script>

<script>
// Tambah produk baru
document.getElementById("add-produk").addEventListener("click", function() {
    let produkContainer = document.getElementById("produk-container");
    let newProduk = produkContainer.querySelector(".produk-item").cloneNode(true);
    newProduk.querySelectorAll('input').forEach(input => input.value = '');
    newProduk.querySelector('select').selectedIndex = 0;
    produkContainer.appendChild(newProduk);
    attachEvent();
});

function attachEvent() {
    document.querySelectorAll('.produk-id').forEach(select => {
        select.addEventListener('change', function() {
            let harga = this.options[this.selectedIndex].dataset.harga || 0;
            this.closest('.produk-item').querySelector('.harga').value = harga;
            calculateTotal();
        });
    });

    document.querySelectorAll('.jumlah').forEach(input => {
        input.addEventListener('input', function() {
            calculateTotal();
        });
    });
    document.querySelectorAll('.harga').forEach(input => {
        input.addEventListener('input', function() {
            calculateTotal();
        });
    });
}

function calculateTotal() {
    let produkItems = document.querySelectorAll('.produk-item');
    let total = 0;
    produkItems.forEach(item => {
        let harga = parseFloat(item.querySelector('.harga').value) || 0;
        let jumlah = parseFloat(item.querySelector('.jumlah').value) || 0;
        total += harga * jumlah;
    });
    document.getElementById('total').value = total;
}

attachEvent();
</script>

</body>
</html>
