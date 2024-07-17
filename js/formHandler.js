function postMedication(formSelector, apiUrl) {
  $(document).ready(function () {
    $(formSelector).on("submit", function (event) {
      event.preventDefault();

      var codeObat = $("#codeObat").val();
      var namaObat = $("#namaObat").val();
      $(formSelector + " input").removeClass("is-invalid");

      var isValid = true;

      if (!codeObat || isNaN(codeObat)) {
        $("#codeObat").addClass("is-invalid");
        isValid = false;
      }
      if (!namaObat) {
        $("#namaObat").addClass("is-invalid");
        isValid = false;
      }
      if (!isValid) {
        return;
      }

      $("#loading").show();

      $.ajax({
        url: apiUrl,
        type: "POST",
        contentType: "application/json",
        data: JSON.stringify({
          codeObat: codeObat,
          namaObat: namaObat,
        }),
        success: function (response) {
          $("#loading").hide();
          alert("Data successfully sent.");
          location.reload();
        },
        error: function (xhr, status, error) {
          $("#loading").hide();
          alert("Error: " + xhr.responseText);
        },
      });
    });
  });
}
function readMedication(apiUrl) {
  $(document).ready(function () {
    function updateTable() {
      $.ajax({
        url: apiUrl,
        type: "GET",
        dataType: "json",
        success: function (data) {
          console.log(data);
          var tableBody = "";
          if (data.length > 0) {
            $.each(data, function (index, row) {
              tableBody += "<tr>";
              tableBody += '<th scope="row">' + (index + 1) + "</th>";
              tableBody += "<td>" + row["code_obat"] + "</td>";
              tableBody += "<td>" + row["nama_obat"] + "</td>";
              tableBody += "<td>";
              tableBody +=
                '<button class="btn btn-info btn-sm info-btn" data-id="' +
                row["id"] +
                '" data-id-obat="' +
                row["id_medic"] +
                '" data-code-obat="' +
                row["code_obat"] +
                '" data-nama-obat="' +
                row["nama_obat"] +
                '" data-status="' +
                row["status"] +
                '" data-jenis-obat="' +
                row["jenis_obat"] +
                '" data-id-pabrik="' +
                row["id_manufaktur"] +
                '" data-last-updated="' +
                row["last_updated"] +
                '">Detail</button> ';
              //   tableBody +=
              //     '<button class="btn btn-warning btn-sm edit-btn">Edit</button> ';
              tableBody +=
                '<button class="btn btn-danger btn-sm delete-btn" data-id="' +
                row["id"] +
                '">Hapus</button>';
              tableBody += "</td>";
              tableBody += "</tr>";
            });
          } else {
            tableBody =
              '<tr><td class="text-center" colspan="4">Data Kosong</td></tr>';
          }
          $("tbody").html(tableBody);

          $(".info-btn").on("click", function () {
            var idObat = $(this).data("id-obat");
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

            $("#modalIdObat").text(idObat);
            $("#modalCodeObat").text(codeObat);
            $("#modalNamaObat").text(namaObat);
            $("#modalStatus").text(status).removeClass().addClass(statusClass);
            $("#modalJenisObat").text(jenisObat);
            $("#modalIdPembuat").text(idPembuat);
            $("#modalLastUpdated").text(lastUpdated);

            $("#infoModal").modal("show");
          });

          $(".delete-btn").on("click", function () {
            var id = $(this).data("id");
            if (confirm("Are you sure you want to delete this record?")) {
              $.ajax({
                url: apiUrl + "?id=" + id,
                type: "DELETE",
                success: function (response) {
                  alert("Record deleted successfully!");
                  updateTable();
                },
                error: function (xhr, status, error) {
                  alert("Error deleting record: " + xhr.responseText);
                },
              });
            }
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
}
