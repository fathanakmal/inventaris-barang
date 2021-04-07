<?php
session_start();

//koneksi ke db
$conn = mysqli_connect("localhost", "root", "", "inventaris-barang");



//tambah barang baru
if (isset($_POST['addnewbarang'])) {
  $namabarang = $_POST['namabarang'];
  $deskripsi = $_POST['deskripsi'];
  $stock = $_POST['stock'];

  $addtotable = mysqli_query($conn, "insert into stock (namabarang, deskripsi, stock) values('$namabarang','$deskripsi','$stock')");
  if ($addtotable) {
    echo "<script>
              alert('Data berhasil Ditambahkan!')
              document.location.href = 'index.php';            
          </script>";
  } else {
    "<script>
              alert('Data Gagal Ditambahkan!')
              document.location.href = 'index.php';            
          </script>";
  }
}


//rambah barang masuk
if (isset($_POST['barangmasuk'])) {
  $barangnya = $_POST['barangnya'];
  $penerima = $_POST['penerima'];
  $qty = $_POST['qty'];

  $cekstocksekarang = mysqli_query($conn, "select * from stock where idbarang='$barangnya'");
  $ambildatanya = mysqli_fetch_array($cekstocksekarang);

  $stocksekarang = $ambildatanya['stock'];
  $tambahkanstocksekarangdenganquantity = $stocksekarang + $qty;

  $addtomasuk = mysqli_query($conn, "insert into barangmasuk (idbarang, keterangan, qty) values('$barangnya','$penerima','$qty')");
  $updatestockmasuk = mysqli_query($conn, "update stock set stock ='$tambahkanstocksekarangdenganquantity' where idbarang='$barangnya'");
  if ($addtomasuk && $updatestockmasuk) {
    echo "<script>
              alert('Data berhasil Ditambahkan!')
              document.location.href = 'masuk.php';            
          </script>";
  } else {
    "<script>
              alert('Data Gagal Ditambahkan!')
              document.location.href = 'masuk.php';            
          </script>";
  }
}


//menambah barang keluar
if (isset($_POST['addbarangkeluar'])) {
  $barangnya = $_POST['barangnya'];
  $penerima = $_POST['penerima'];
  $qty = $_POST['qty'];

  $cekstocksekarang = mysqli_query($conn, "select * from stock where idbarang='$barangnya'");
  $ambildatanya = mysqli_fetch_array($cekstocksekarang);

  $stocksekarang = $ambildatanya['stock'];

  if ($stocksekarang >= $qty) {
    //kalau stok cukup
    $tambahkanstocksekarangdenganquantity = $stocksekarang - $qty;

    $addtokeluar = mysqli_query($conn, "insert into barangkeluar (idbarang, penerima, qty) values('$barangnya','$penerima','$qty')");
    $updatestockmasuk = mysqli_query($conn, "update stock set stock ='$tambahkanstocksekarangdenganquantity' where idbarang='$barangnya'");
    if ($addtokeluar && $updatestockmasuk) {
      echo "<script>
              alert('Data berhasil Ditambahkan!')
              document.location.href = 'keluar.php';            
          </script>";
    } else {
      "<script>
              alert('Data Gagal Ditambahkan!')
              document.location.href = 'keluar.php';            
          </script>";
    }
  } else {
    //kalau stok kurang
    echo '
      <script>
        alert("Jumlah Barang Tidak Cukup");
        window.location.href="keluar.php";
      </script>';
  }
}

//update info barang
if (isset($_POST['updatebarang'])) {
  $idb = $_POST['idb'];
  $namabarang = $_POST['namabarang'];
  $deskripsi = $_POST['deskripsi'];

  $update = mysqli_query($conn, "update stock set namabarang='$namabarang', deskripsi='$deskripsi' where idbarang = '$idb'");
  if ($update) {
    header('location:index.php');
  } else {
    "<script>
        alert('Data Gagal Diubah!')
        document.location.href = 'index.php';            
    </script>";
  }
}

//menghapus barang
if (isset($_POST['hapusbarang'])) {
  $idb = $_POST['idb'];

  $hapus = mysqli_query($conn, "delete from stock where idbarang = '$idb'");
  if ($update) {
    header('location:index.php');
  } else {
    "<script>
        alert('Data Gagal Diubah!')
        document.location.href = 'index.php';            
    </script>";
  }
}

//mengubah data barang masuk 
if (isset($_POST['updatebarangmasuk'])) {
  $idb = $_POST['idb'];
  $idm = $_POST['idm'];
  $deskripsi = $_POST['keterangan'];
  $qty = $_POST['qty'];

  $lihatstock = mysqli_query($conn, "select * from stock where idbarang='$idb'");
  $stocknya = mysqli_fetch_array($lihatstock);
  $stockskrg = $stocknya['stock'];

  $qtyskrg = mysqli_query($conn, "select * from barangmasuk where idmasuk='$idm'");
  $qtynya = mysqli_fetch_array($qtyskrg);
  $qtyskrg = $qtynya['qty'];

  if ($qty > $qtyskrg) {
    $selisih = $qty - $qtyskrg;
    $kurangin = $stockskrg + $selisih;
    $kurangistocknya = mysqli_query($conn, "update stock set stock='$kurangin' where idbarang='$idb'");
    $updatenya = mysqli_query($conn, "update barangmasuk set qty='$qty', keterangan='$deskripsi' where idmasuk='$idm'");
    if ($kurangistocknya && $updatenya) {
      header('location:masuk.php');
    } else {
      echo 'Gagal';
      header('location:masuk.php');
    }
  } else {
    $selisih = $qtyskrg - $qty;
    $kurangin = $stockskrg - $selisih;
    $kurangistocknya = mysqli_query($conn, "update stock set stock='$kurangin' where idbarang='$idb'");
    $updatenya = mysqli_query($conn, "update barangmasuk set qty='$qty', keterangan='$deskripsi' where idmasuk='$idm'");
    if ($kurangistocknya && $updatenya) {
      header('location:masuk.php');
    } else {
      echo 'Gagal';
      header('location:masuk.php');
    }
  }
}


//menghapus barang masuk
if (isset($_POST['hapusbarangmasuk'])) {
  $idb = $_POST['idb'];
  $qty = $_POST['kty'];
  $idm = $_POST['idm'];

  $getdatastock = mysqli_query($conn, "select * from stock where idbarang='$idb'");
  $data = mysqli_fetch_array($getdatastock);
  $stok = $data['stock'];

  $selisih = $stok - $qty;

  $update = mysqli_query($conn, "update stock set stock ='$selisih' where idbarang='$idb'");
  $hapusdata = mysqli_query($conn, "delete from barangmasuk where idmasuk='$idm'");

  if ($update && $hapusdata) {
    header('location:masuk.php');
  } else {
    header('location:masuk.php');
  }
}


//mengubah data barang keluar 
if (isset($_POST['updatebarangkeluar'])) {
  $idb = $_POST['idb'];
  $idk = $_POST['idk'];
  $penerima = $_POST['penerima'];
  $qty = $_POST['qty'];

  $lihatstock = mysqli_query($conn, "select * from stock where idbarang='$idb'");
  $stocknya = mysqli_fetch_array($lihatstock);
  $stockskrg = $stocknya['stock'];

  $qtyskrg = mysqli_query($conn, "select * from barangkeluar where idkeluar='$idk'");
  $qtynya = mysqli_fetch_array($qtyskrg);
  $qtyskrg = $qtynya['qty'];

  if ($qty > $qtyskrg) {
    $selisih = $qty - $qtyskrg;
    $kurangin = $stockskrg - $selisih;
    $kurangistocknya = mysqli_query($conn, "update stock set stock='$kurangin' where idbarang='$idb'");
    $updatenya = mysqli_query($conn, "update barangkeluar set qty='$qty', penerima='$penerima' where idkeluar='$idk'");
    if ($kurangistocknya && $updatenya) {
      header('location:keluar.php');
    } else {
      echo 'Gagal';
      header('location:keluar.php');
    }
  } else {
    $selisih = $qtyskrg - $qty;
    $kurangin = $stockskrg + $selisih;
    $kurangistocknya = mysqli_query($conn, "update stock set stock='$kurangin' where idbarang='$idb'");
    $updatenya = mysqli_query($conn, "update barangkeluar set qty='$qty', penerima='$penerima' where idkeluar='$idk'");
    if ($kurangistocknya && $updatenya) {
      header('location:keluar.php');
    } else {
      echo 'Gagal';
      header('location:keluar.php');
    }
  }
}


//menghapus barang keluar
if (isset($_POST['hapusbarangkeluar'])) {
  $idb = $_POST['idb'];
  $qty = $_POST['kty'];
  $idk = $_POST['idk'];

  $getdatastock = mysqli_query($conn, "select * from stock where idbarang='$idb'");
  $data = mysqli_fetch_array($getdatastock);
  $stok = $data['stock'];

  $selisih = $stok + $qty;

  $update = mysqli_query($conn, "update stock set stock ='$selisih' where idbarang='$idb'");
  $hapusdata = mysqli_query($conn, "delete from barangkeluar where idkeluar='$idk'");

  if ($update && $hapusdata) {
    header('location:keluar.php');
  } else {
    header('location:keluar.php');
  }
}