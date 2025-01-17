<!DOCTYPE html>
<html lang="en">
<?php
    include("header.php");
?>

       <!-- Begin Page Content -->
       <div class="container-fluid">

<!-- Page Heading -->
<h1 class="h3 mb-2 text-gray-800">Data Konfirmasi Pembayaran</h1>


<!-- DataTales Example -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
     
    </div>
  <div class="card-body">
  <?php  
      if (empty($_GET['alert'])) {
        echo "";
      } 
  
      elseif ($_GET['alert'] == 1) {
        echo "<div class='alert alert-danger alert-dismissable'>
                <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
                <h4>  <i class='icon fa fa-times-circle'></i> Gagal Memasukan Data</h4>
                Data yang anda masukan salah, silahkan di check kembali.
              </div>";
      }
      elseif ($_GET['alert'] == 2) {
        echo "<div class='alert alert-success alert-dismissable'>
                <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
                <h4>  <i class='icon fa fa-check-circle'></i> Success!</h4>
                Anda telah berhasil mengupdate data.
              </div>";
    }
    elseif ($_GET['alert'] == 3) {
      echo "<div class='alert alert-info alert-dismissable'>
              <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
              <h4>  <i class='icon fa fa-check-circle'></i> Gagal!</h4>
              Terjadi Kesalahan Pada Server Mohon Dicoba Kembali
            </div>";
  }
      ?>
    <div class="table-responsive">
      <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
        <thead>
          <tr>
            <th>Nomor</th>
            <th>Rekening Tujuan</th>
            <th>Rekening Pengirim</th>
            <th>Nama Pengirim</th>
            <th>Jumlah Transfer</th>
            <th>Tanggal Transfer</th>
            <th>Bukti Transfer</th>
            <th>Status</th>
            <th>Action</th>
          </tr>
        </thead>
        <tfoot>
          <tr>
            <th>Nomor</th>
            <th>Rekening Tujuan</th>
            <th>Rekening Pengirim</th>
            <th>Nama Pengirim</th>
            <th>Jumlah Transfer</th>
            <th>Tanggal Transfer</th>
            <th>Bukti Transfer</th>
            <th>Status</th>
            <th>Action</th>
          </tr>
        </tfoot>
        <tbody>
        <?php
                  include("proses/koneksi.php");
                  $id_u = $_SESSION['id_user'];
                  if($_SESSION['level'] == "1" || $_SESSION['level'] == "2"){
                      //status 2  = menunggu konfirmasi
                    $query = "SELECT * FROM konfirmasi_pembayaran inner join sesi_transaksi on sesi_transaksi.id_sesi = konfirmasi_pembayaran.fk_id_sesi_transaksi INNER JOIN user on sesi_transaksi.id_distributor = user.id_user WHERE ( user.fk_id_level = '1') OR (  user.fk_id_level = '2') ";
                  } else {
                    $query = "SELECT * FROM konfirmasi_pembayaran inner join sesi_transaksi on sesi_transaksi.id_sesi = konfirmasi_pembayaran.fk_id_sesi_transaksi INNER JOIN user on sesi_transaksi.id_distributor = user.id_user  WHERE   sesi_transaksi.id_distributor = '$id_u' ";
                  }
                  
                  $nomor =1;
                  $tampilin = mysqli_query($koneksi, $query);
                  while($output = mysqli_fetch_array($tampilin)){
                    $sesi = $output['id_sesi'];
                    $id = $output['id_konfirmasi'];
                    //rekening tujuan
                    if($_SESSION['level'] == "1" || $_SESSION['level'] == "2"){
                        $nomorRekeningTujuan = getNamaRekening($koneksi, $output['fk_id_rekening']);
                    }
                    else {
                        $namaBank = $output['bank_rekening'];
                        $noRekening = $output['nomor_rekening'];
                        $gabungan = $namaBank."-".$noRekening;
                        $nomorRekeningTujuan = $gabungan;
                    }
                    $bankPengirim = $output['bank_pengirim'];
                    $noRekPengirim = $output['nomor_rekening_pengirim'];
                    $dataPengirim = $bankPengirim."-".$noRekPengirim;
                    $namaPengirim = $output['nama_pengirim'];
                    $jumlahPengirim = $output['jumlah_transfer'];
                    $tTransfer = $output['tgl_transfer'];
                    $gambar = $output['bukti_transfer'];
                    $status = $output['konfirmasi_status'];
                    $tujuan = $output['fk_id_u']
                  
                  ?>
                    <tr>
                        <td><?php echo $nomor; ?></td>
                        <td><?php echo $nomorRekeningTujuan; ?></td>
                        <td><?php echo $dataPengirim; ?></td>
                        <td><?php echo $namaPengirim ?></td>
                        <td><?php echo rupiah($jumlahPengirim); ?></td>
                        <td><?php echo date("d-m-Y", strtotime($tTransfer)); ?></td>
                       
                        <td><image height ="100px" width ="100px" src="<?php echo getDirectoryBukti().$gambar ;?>"></td>
                        <td><?php 
                        if($status == '1'){
                            echo "Lunas"; 
                        }else if($status == '0') {
                            echo "Belum Diterima";
                        }else {
                            echo "Menunggu Konfirmasi";
                        }
                        ?></td>
                       <td>
                       <?php if($status == '2'){
                            ?>
                            <a href="proses/prosesKonfirmasi.php?d=<?php echo $tujuan;?>&u=<?php echo $id_u; ?>&code=1&level=<?php echo $_SESSION['level']; ?>&id=<?php echo $id;?>"><button type='submit'  class='btn btn-success btn-flat btn_edit'
                           > Sudah Terima</button></a>
                            <br>
                            <br>
                            <a href="proses/prosesKonfirmasi.php?d=<?php echo $tujuan;?>&u=<?php echo $id_u; ?>&code=2&level=<?php echo $_SESSION['level']; ?>&id=<?php echo $id;?>"><button type='submit'  class='btn btn-danger btn-flat btn_edit'
                           > Belum Terima</button>  </td></a>
                            <br>
                            <br>
                            <a data-toggle="modal" class="btn" href="#myModal" id="<?php echo $sesi; ?>">Detail</a>
                            <?php
                        }else {
                            ?>
                            <a data-toggle="modal" class="btn" href="#myModal" id="<?php echo $sesi; ?>">Detail</a>
                            <?php
                        }
                        ?>
                    </tr>
                    <?php
                    $nomor +=1;
                 }
          ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

</div>
<!-- /.container-fluid -->

      </div>
      <!-- End of Main Content -->
      <div class="modal fade" id="myModal" role="dialog">
            <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div id="modal-body" class="modal-body">
                    
                </div>
            </div>
            </div>
        </div>



   <!-- Footer -->
   <footer class="sticky-footer bg-white">
        <div class="container my-auto">
          <div class="copyright text-center my-auto">
            <span>Copyright &copy; <?php echo "Dak Dak";?></span>
          </div>
        </div>
      </footer>
      <!-- End of Footer -->

    </div>
    <!-- End of Content Wrapper -->

  </div>
  <!-- End of Page Wrapper -->

  <!-- Scroll to Top Button-->
  <a class="scroll-to-top rounded" href="#page-top">
    <i class="fas fa-angle-up"></i>
  </a>

  <!-- Logout Modal-->
  <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
          <button class="close" type="button" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span>
          </button>
        </div>
        <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
        <div class="modal-footer">
          <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
          <a class="btn btn-primary" href="proses/prosesLogout.php">Logout</a>
        </div>
      </div>
    </div>
  </div>

  <!-- Bootstrap core JavaScript-->
  <script src="vendor/jquery/jquery.min.js"></script>
  <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

  <!-- Core plugin JavaScript-->
  <script src="vendor/jquery-easing/jquery.easing.min.js"></script>

  <!-- Custom scripts for all pages-->
  <script src="js/sb-admin-2.min.js"></script>

  <!-- Page level plugins -->
  <script src="vendor/chart.js/Chart.min.js"></script>
  <script src="vendor/datatables/jquery.dataTables.min.js"></script>
  <script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>

  <!-- Page level custom scripts -->
  <script src="js/demo/chart-area-demo.js"></script>
  <script src="js/demo/chart-pie-demo.js"></script>
  <script src="js/demo/datatables-demo.js"></script>
  <script>
		 $(document).ready(function() {
        $(".btn_edit").click(function(event){
          var id = $(this).data('id');
          var pembelian = $(this).data('pembelian');

          var deposit = $(this).data('deposit');
          
        
          $("#id").val(id);
          $("#pembelian").val(pembelian);
        
          
          
          $("#deposit").val(deposit);
          
          
          /*
          $('#form_send').form('clear');
          $("#myModal").modal({
            backdrop: "static"
          });
          */
          
        });
        $("a[data-toggle=modal]").click(function() {
            var id_beli = $(this).attr('id');
            $.ajax({
                type: "POST",
                dataType: "html",
                url: "proses/detailProdukBelanja.php?id="+id_beli,
                success: function(msg){
                    $('#myModal').show();
                    $('#modal-body').show().html(msg); //this part to pass the var                                                                                                       
                }
            });       
        });
  });
  </script>
  
</body>

</html>