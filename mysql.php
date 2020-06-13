<?php
$sql = new mysqli("localhost", "root", "", "brainforce");
if ($sql->connect_error) {
  die($sql->connect_error);
}
?>
