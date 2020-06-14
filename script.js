$(function() {
  $.get("table.php", function(data) {
    if (data["ok"]) {
      renderTable(data);
      $("#total1").text(data["totalStorage1"]);
      $("#total2").text(data["totalStorage2"]);
      $("#avgPrice").text(data["avgPrice"]);
      $("#avgPriceBulk").text(data["avgPriceBulk"]);
    }
  });

  $("#filter").submit(e => {
    e.preventDefault();

    const formData = $("#filter").serialize();
    $.get("table.php", formData, function(data) {
      if (data["ok"]) {
        $("#table").empty();
        $("#error").empty();
        renderTable(data);
      } else {
        $("#error").text(data["errorText"]);
      }
    });
  });
});

function renderTable(data) {
  data["data"].forEach(row => $("#table").append(renderRow(row,
    data["minPriceBulk"], data["maxPrice"])));
}

function renderRow(row, minPrice, maxPrice) {
  var tr = "<tr><td>";
  tr += row["product_name"] + "</td><td";
  if (row["price"] === maxPrice) {
    tr += " class='red'";
  }
  tr += ">" + row["price"] + "</td><td";
  if (row["price_bulk"] === minPrice) {
    tr += " class='green'";
  }
  tr += ">" + row["price_bulk"] + "</td><td>" + row["quantity_1"] + "</td><td>";
  tr += row["quantity_2"] + "</td><td>" + row["country"] + "</td><td>";
  tr += row["notes"] + "</td></tr>";

  return tr;
}
