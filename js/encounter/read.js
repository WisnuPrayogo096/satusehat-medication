$(document).ready(function () {
  function updateTable() {
    $.ajax({
      url: "get-data/encounter.php",
      type: "GET",
      dataType: "json",
      success: function (data) {
        console.log(data);
        var tableBody = "";
        if (data.length > 0) {
          $.each(data, function (index, row) {
            tableBody += "<tr>";
            tableBody += '<th scope="row">' + (index + 1) + "</th>";
            tableBody += "<td>" + row["id_patient"] + "</td>";
            tableBody += "<td>" + row["nama"] + "</td>";
            tableBody += "<td>";
            tableBody +=
              '<button class="btn btn-success info-btn" data-nama="' +
              row["nama"] +
              '" data-id-pasien="' +
              row["id_patient"] +
              '" data-id-encounter="' +
              row["id_encounter"] +
              '">';
            tableBody += '<i class="fas fa-info-circle"></i>';
            tableBody += "</button>";
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
          var nama = $(this).data("nama");
          var idPasien = $(this).data("id-pasien");
          var idEncounter = $(this).data("id-encounter");

          // Isi data ke dalam modal
          $("#modalNama").text(nama);
          $("#modalIdPasien").text(idPasien);
          $("#modalIdEncounter").text(idEncounter);

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
  setInterval(updateTable, 5000); // Memperbarui setiap 5 detik
});
