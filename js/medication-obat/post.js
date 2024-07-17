$(document).ready(function () {
  $("#addEncounterForm").on("submit", function (event) {
    event.preventDefault();

    var codeObat = $("#codeObat").val();
    var namaObat = $("#namaObat").val();
    $("#addEncounterForm input").removeClass("is-invalid");

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
      url: "../../api/payload/post-medication-obat.php",
      type: "POST",
      contentType: "application/json",
      data: JSON.stringify({
        codeObat: codeObat,
        namaObat: namaObat,
      }),
      success: function (response) {
        $("#loading").hide();
        alert("Data successfully sent!");
        location.reload();
      },
      error: function (xhr, status, error) {
        $("#loading").hide();
        alert("Error: " + xhr.responseText);
      },
    });
  });
});
