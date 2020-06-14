<?php
include_once("mysql.php");

$query = "SELECT * FROM pricelist";
if (isset($_GET["price_type"]) &&
  isset($_GET["min_price"]) && isset($_GET["max_price"]) &&
  isset($_GET["compare"]) && isset($_GET["quantity"])) {
  $type = $_GET["price_type"] == 2 ? "price_bulk" : "price";
  $min_price = $_GET["min_price"];
  $max_price = $_GET["max_price"];
  $compare = $_GET["compare"] == 2 ? "<" : ">";
  $quantity = $_GET["quantity"];

  if (empty($min_price) || empty($max_price) || empty($quantity) ||
    !(is_numeric($min_price) && is_numeric($max_price) && is_numeric($quantity))) {
    error_response("Все поля должны быть численными");
    die();
  }

  $query = "SELECT * FROM pricelist WHERE " . $type . " BETWEEN " . $min_price .
  " AND " . $max_price . " AND (quantity_1 + quantity_2) " . $compare . " " . $quantity;
}

$result = $sql->query($query);

if ($result->num_rows > 0) {
  $response = array();
  $temp_table = array();

  $response["totalStorage1"] = 0;
  $response["totalStorage2"] = 0;
  $response["maxPrice"] = 0;
  $response["minPriceBulk"] = 0;

  $total_price = 0;
  $total_price_bulk = 0;
  $total_items = 0;

  while ($row = $result->fetch_assoc()) {
    if ($response["maxPrice"] < $row["price"]) {
      $response["maxPrice"] = $row["price"];
    }

    if ($row["price_bulk"] != 0 && ($response["minPriceBulk"] == 0 ||
      $response["minPriceBulk"] > $row["price_bulk"])) {
      $response["minPriceBulk"] = $row["price_bulk"];
    }

    $response["totalStorage1"] += $row["quantity_1"];
    $response["totalStorage2"] += $row["quantity_2"];

    if ($row["price"] != 0) {
      $total_price += $row["price"];
      $total_price_bulk += $row["price_bulk"];
      $total_items += 1;
    }

    $low_stock = ($row["quantity_1"] + $row["quantity_2"] < 20) ? "Осталось мало!! Срочно докупите!!!" : "";
    $row["notes"] = $low_stock;
    array_push($temp_table, $row);
  }

  $response["avgPrice"] = round($total_price / $total_items, 2);
  $response["avgPriceBulk"] = round($total_price_bulk / $total_items, 2);

  $response["data"] = $temp_table;
  $response["ok"] = TRUE;

  json_response($response);
} else {
  error_response("Позиций не найдено");
}

function json_response($resp) {
  header("Content-Type: application/json");
  echo json_encode($resp);
}

function error_response($text) {
  $response = array();
  $response["ok"] = FALSE;
  $response["errorText"] = $text;
  json_response($response);
}
?>
