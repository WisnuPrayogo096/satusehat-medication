$(document).ready(function () {
  function updateTable() {
    $.ajax({
      url: "get-data/medication-obat.php",
      type: "GET",
      dataType: "json",
      success: function (data) {
        console.log(data);
        var tableBody = "";
        if (data.length > 0) {
          $.each(data, function (index, row) {
            var statusClass =
              row["status"].toLowerCase() === "active"
                ? "text-success font-weight-bold"
                : "text-danger font-weight-bold";

            tableBody += "<tr>";
            tableBody += '<th scope="row">' + (index + 1) + "</th>";
            tableBody += "<td>" + row["code_obat"] + "</td>";
            tableBody += "<td>" + row["nama_obat"] + "</td>";
            tableBody += "<td>";
            tableBody +=
              '<button class="btn btn-info btn-sm info-btn" data-code-obat="' +
              row["code_obat"] +
              '" data-nama-obat="' +
              row["nama_obat"] +
              '" data-status="' +
              row["status"] +
              '" data-jenis-obat="' +
              row["jenis_obat"] +
              '" data-id-pabrik="' +
              row["id_pabrik"] +
              '" data-last-updated="' +
              row["last_updated"] +
              '">Detail</button> ';
            tableBody +=
              '<button class="btn btn-warning btn-sm">Edit</button> ';
            tableBody += '<button class="btn btn-danger btn-sm">Hapus</button>';
            tableBody += "</td>";
            tableBody += "</tr>";
          });
        } else {
          tableBody =
            '<tr><td class="text-center" colspan="4">Data Kosong</td></tr>';
        }
        $("tbody").html(tableBody);

        // Menambahkan event listener untuk tombol info-btn setelah tabel diperbarui
        $(".info-btn").on("click", function () {
          var codeObat = $(this).data("code-obat");
          var namaObat = $(this).data("nama-obat");
          var status = $(this).data("status");
          var statusClass =
            status.toLowerCase() === "active"
              ? "text-success font-weight-bold"
              : "text-danger font-weight-bold";
          var jenisObat = $(this).data("jenis-obat");
          var idPembuat = $(this).data("id-pabrik");
          var lastUpdated = $(this).data("last-updated");

          // Isi data ke dalam modal
          $("#modalCodeObat").text(codeObat);
          $("#modalNamaObat").text(namaObat);
          $("#modalStatus").text(status).removeClass().addClass(statusClass);
          $("#modalJenisObat").text(jenisObat);
          $("#modalIdPembuat").text(idPembuat);
          $("#modalLastUpdated").text(lastUpdated);

          // Tampilkan modal
          $("#infoModal").modal("show");
        });
      },
      error: function (xhr, status, error) {
        console.error(xhr.responseText);
      },
    });
  }

  updateTable();
  setInterval(updateTable, 5000);
});
