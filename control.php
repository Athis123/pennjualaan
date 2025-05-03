<?php
// Koneksi database
$koneksi = new mysqli("localhost", "root", "", "penjualan");
if ($koneksi->connect_error) {
    die("Koneksi gagal: " . $koneksi->connect_error);
}

// Class untuk login
class db {
    public $koneksi;

    public function __construct() {
        $this->koneksi = new mysqli("localhost", "root", "", "penjualan");
        if ($this->koneksi->connect_error) {
            die("Koneksi gagal: " . $this->koneksi->connect_error);
        }
    }

    public function get_user($username, $password) {
        // Untuk menghindari SQL Injection
        $username = $this->koneksi->real_escape_string($username);
        $password = $this->koneksi->real_escape_string($password);
        $query = "SELECT * FROM user WHERE username='$username' AND password='$password'";
        return $this->koneksi->query($query);
    }
}

// Handle tambah produk
if (isset($_POST['tambah'])) {
    $nama = $_POST['nama'];
    $kode = $_POST['kode'];
    $harga = $_POST['harga'];
    $stok = $_POST['stok'];

    $stmt = $koneksi->prepare("INSERT INTO produk (nama, kode, harga, stok) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssii", $nama, $kode, $harga, $stok);
    $stmt->execute();
    $stmt->close();

    header("Location: produk.php");
    exit;
}

// Handle update produk
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $nama = $_POST['nama'];
    $kode = $_POST['kode'];
    $harga = $_POST['harga'];
    $stok = $_POST['stok'];

    $stmt = $koneksi->prepare("UPDATE produk SET nama=?, kode=?, harga=?, stok=? WHERE id=?");
    $stmt->bind_param("ssiii", $nama, $kode, $harga, $stok, $id);
    $stmt->execute();
    $stmt->close();

    header("Location: produk.php");
    exit;
}

// Handle hapus produk
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];

    $stmt = $koneksi->prepare("DELETE FROM produk WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();

    header("Location: produk.php");
    exit;
}

// Ambil data produk untuk edit
$edit = null;
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];

    $stmt = $koneksi->prepare("SELECT * FROM produk WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $edit = $result->fetch_assoc();
    }

    $stmt->close();
}

// Ambil semua data produk
$produk = $koneksi->query("SELECT * FROM produk");
if (!$produk) {
    die("Query produk gagal: " . $koneksi->error);
}
?>
