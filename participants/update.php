<?php
include '../includes/auth_check.php';
include '../config/database.php';

$id_user=$_SESSION['id_user'];

$id_peserta=(int)$_POST['id_peserta'];
$id_event=(int)$_POST['id_event'];

$nama=trim($_POST['nama']);
$instansi=trim($_POST['instansi']);
$email=trim($_POST['email']);
$no_hp=trim($_POST['no_hp']);
$status_kehadiran=trim($_POST['status_kehadiran']);

$cek=mysqli_query($conn,

"SELECT id_event

FROM events

WHERE id_event='$id_event'

AND id_user='$id_user'");

if(mysqli_num_rows($cek)==0){

header("Location:../events/index.php");
exit;

}

$query="UPDATE participants SET

nama='$nama',
instansi='$instansi',
email='$email',
no_hp='$no_hp',
status_kehadiran='$status_kehadiran'

WHERE id_peserta='$id_peserta'

AND id_event='$id_event'";

if(mysqli_query($conn,$query)){

$_SESSION['success']="Peserta berhasil diperbarui.";

header("Location:index.php?id_event=$id_event");
exit;

}else{

die(mysqli_error($conn));

}