<?php
$koneksi = new mysqli("localhost", "root", "", "penjualan");

// Tambah data
if (isset($_POST['tambah'])) {
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $alamat = mysqli_real_escape_string($koneksi, $_POST['alamat']);
    $kontak = mysqli_real_escape_string($koneksi, $_POST['kontak']);

    // Perbaiki query agar sesuai dengan format SQL
    $koneksi->query("INSERT INTO perusahaan (nama, alamat, kontak) VALUES ('$nama', '$alamat', '$kontak')");
    header("Location: perusahaan.php");
    exit;
}

// Hapus data
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    $koneksi->query("DELETE FROM perusahaan WHERE id=$id");
    header("Location: perusahaan.php");
    exit;
}

// Update data
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $alamat = mysqli_real_escape_string($koneksi, $_POST['alamat']);
    $kontak = mysqli_real_escape_string($koneksi, $_POST['kontak']);
    $koneksi->query("UPDATE perusahaan SET nama='$nama', alamat='$alamat', kontak='$kontak' WHERE id=$id");
    header("Location: perusahaan.php");
    exit;
}

// Ambil data untuk form edit
$edit = null;
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $result = $koneksi->query("SELECT * FROM perusahaan WHERE id=$id");
    $edit = $result->fetch_assoc();
}

$data = $koneksi->query("SELECT * FROM perusahaan");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data perusahaan</title>
    <link href="asset/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link href="asset/css/sb-admin-2.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body id="page-top">
    <!-- Page Wrapper -->
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
                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>
                    <ul class="navbar-nav ml-auto">
                        <!-- User Info -->
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small">Welcome User</span>
                                <i class="fas fa-user-circle fa-2x text-gray-600"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                                <a class="dropdown-item" href="logout.php">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Logout
                                </a>
                            </div>
                        </li>
                    </ul>
                </nav>
                <!-- End of Topbar -->

                <!-- Main Content -->
                <div class="container-fluid py-4">
                    <h2>Menu perusahaan</h2>

                    <!-- Form Tambah/Edit perusahaan -->
                    <form method="post" class="mb-3">
                        <input type="hidden" name="id" value="<?= $edit['id'] ?? '' ?>">
                        <div class="row mb-2">
                            <div class="col">
                                <input type="text" name="nama" class="form-control" placeholder="Nama" value="<?= $edit['nama'] ?? '' ?>" required>
                            </div>

                            <div class="col">
                                <input type="text" name="alamat" class="form-control" placeholder="Alamat" value="<?= $edit['alamat'] ?? '' ?>" required>
                            </div>
                            <div class="col">
                                <input type="text" name="kontak" class="form-control" placeholder="kontak" value="<?= $edit['kontak'] ?? '' ?>" required>
                            </div>
                            <div class="col">
                                <button type="submit" name="<?= $edit ? 'update' : 'tambah' ?>" class="btn btn-primary"><?= $edit ? 'Update' : 'Tambah' ?></button>
                            </div>
                        </div>
                    </form>

                    <!-- Tabel perusahaan -->
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>Alamat</th>
                                <th>Kontak</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php while ($row = $data->fetch_assoc()): ?>
                            <tr>
                                <td><?= $row['nama'] ?></td>
                                <td><?= $row['kontak'] ?></td>
                                <td><?= $row['alamat'] ?></td>
                                <td>
                                    <a href="?edit=<?= $row['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                                    <a href="?hapus=<?= $row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Apakah and yakin?')">Hapus</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                        </tbody>
                    </table>
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

</body>
</html>
