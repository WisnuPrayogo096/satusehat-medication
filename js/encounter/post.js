$(document).ready(function () {
  $("#addEncounterForm").on("submit", function (event) {
    event.preventDefault();

    var idPasien = $("#idPasien").val();
    var nama = $("#nama").val();

    $("#loading").show();

    $.ajax({
      url: "api/payload/post-encounter.php",
      type: "POST",
      contentType: "application/json",
      data: JSON.stringify({
        idPasien: idPasien,
        nama: nama,
      }),
      success: function (response) {
        // Hide the loading spinner
        $("#loading").hide();
        alert("Encounter added successfully: " + JSON.stringify(response));
        location.reload();
      },
      error: function (xhr, status, error) {
        // Hide the loading spinner
        $("#loading").hide();
        alert("Error: " + xhr.responseText);
      },
    });
  });
});
