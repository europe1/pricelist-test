<?php
$file = fopen("pricelist.csv", "r");
if ($file !== FALSE) {
  $row = 1;
  while (($line = fgetcsv($file)) !== FALSE) {
    if ($row != 1) {
      require_once("mysql.php");
      $query = "INSERT INTO pricelist (product_name, price, price_bulk,
      quantity_1, quantity_2, country) VALUES (?, ?, ?, ?, ?, ?)";
      $statement = $sql->prepare($query);
      $statement->bind_param("sddiis", $line[0], $line[1], $line[2], $line[3], $line[4], $line[5]);
      $statement->execute();
    }
    $row += 1;
  }
  fclose($file);
}
?>
