<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medication Request</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
    .delete-field {
        cursor: pointer;
    }

    .modal-dialog.modal-wide {
        max-width: 40%;
    }

    .ui-autocomplete {
        max-height: 200px;
        overflow-y: auto;
        overflow-x: hidden;
        padding-right: 20px;
        z-index: 1060 !important;
    }
    </style>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
</head>

<body>
    <div class="container mt-5">
        <div class="mb-3 mt-3">
            <a href="index.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i>
            </a>
        </div>
        <h1>Medication Request Dashboard</h1>
        <h5>Medication Request adalah sumber daya yang digunakan untuk merepresentasikan permintaan atau resep obat yang
            dibuat oleh seorang praktisi kesehatan untuk seorang pasien.</h5>

        <div id="medication-request">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>ID Encounter</th>
                        <th>Nama Pasien</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    require 'get-data/koneksi.php';

                    $sql = "SELECT id, id_encounter, id_patient, nama FROM encounter";
                    $result = $conn->query($sql);

                    $encounters = array();
                    if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            $encounters[] = $row;
                        }
                    }

                    $sql2 = "SELECT id_medic, code_obat, nama_obat FROM medication_obat";
                    $result2 = $conn->query($sql2);

                    $medication_obat = array();
                    if ($result2->num_rows > 0) {
                        while($row2 = $result2->fetch_assoc()) {
                            $medication_obat[] = $row2;
                        }
                    }
                    $conn->close();
                    
                    $no = 1;
                    foreach ($encounters as $encounter) {
                        echo "<tr>";
                        echo "<td>" . $no++ . "</td>";
                        echo "<td>" . $encounter['id_encounter'] . "</td>";
                        echo "<td>" . $encounter['nama'] . "</td>";
                        echo "<td><button class='btn btn-info request-btn' data-id_encounter='" . $encounter['id_encounter'] . "' data-id_patient='" . $encounter['id_patient'] . "' data-nama='" . $encounter['nama'] . "' data-toggle='modal' data-target='#medicationRequest'>Request</button></td>";
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
            <div id="notification"></div>
        </div>

        <!-- Add Encounter Modal -->
        <div class="modal fade" id="medicationRequest" tabindex="-1" aria-labelledby="medicationRequestLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-wide">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="medicationRequestLabel">Request Obat</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="addEncounterForm">
                            <input type="hidden" id="idEncounter" name="idEncounter">
                            <input type="hidden" id="idPatient" name="idPatient">
                            <input type="hidden" id="namaPasien" name="namaPasien">
                            <input type="hidden" id="idMedic" name="idMedic">
                            <div class="form-group">
                                <label for="codeObat">Code Obat</label>
                                <input type="text" class="form-control" id="codeObat" name="codeObat" required readonly>
                            </div>
                            <div class="form-group">
                                <label for="namaObat">Nama Obat</label>
                                <input type="text" class="form-control" id="namaObat" name="namaObat">
                            </div>
                            <div class="form-group">
                                <label for="instruksiDosis">Instruksi Dosis</label>
                                <textarea class="form-control" id="instruksiDosis" name="instruksiDosis"
                                    rows="3"></textarea>
                            </div>
                            <div class="form-group">
                                <label for="instruksiPasien">Instruksi Pasien</label>
                                <textarea class="form-control" id="instruksiPasien" name="instruksiPasien"
                                    rows="3"></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary mt-3">Simpan</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const medicationData = <?php echo json_encode($medication_obat); ?>;

        $("#namaObat").autocomplete({
            source: medicationData.map(item => item.nama_obat),
            select: function(event, ui) {
                const selectedObat = medicationData.find(item => item.nama_obat === ui.item.value);
                if (selectedObat) {
                    $("#codeObat").val(selectedObat.code_obat);
                    $("#idMedic").val(selectedObat.id_medic);
                }
            }
        });

        $('.request-btn').on('click', function() {
            $('#idEncounter').val($(this).data('id_encounter'));
            $('#idPatient').val($(this).data('id_patient'));
            $('#namaPasien').val($(this).data('nama'));
        });

        $('#addEncounterForm').on('submit', function(event) {
            event.preventDefault();
            $.ajax({
                url: 'api/payload/post-medication-req.php',
                method: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({
                    idMedic: $('#idMedic').val(),
                    codeObat: $('#codeObat').val(),
                    namaObat: $('#namaObat').val(),
                    instruksiDosis: $('#instruksiDosis').val(),
                    instruksiPasien: $('#instruksiPasien').val(),
                    idEncounter: $('#idEncounter').val(),
                    idPatient: $('#idPatient').val(),
                    namaPasien: $('#namaPasien').val()
                }),
                success: function(response) {
                    alert('Request berhasil disimpan. Response: ' + response);
                    location.reload();
                },
                error: function(xhr, status, error) {
                    alert("Error: " + xhr.responseText);
                },
                // success: function(response) {
                //     $('#notification').html('Request berhasil disimpan. Response: ' +
                //         response);
                //     $('#notification').css('color', 'green');
                //     $('#medicationRequest').modal('hide');
                // },
                // error: function(xhr, status, error) {
                //     $('#notification').html('<textarea readonly>Error: ' + xhr
                //         .responseText + '</textarea>');
                //     $('#notification').css('color', 'red');
                // }

            });
        });
    });
    </script>
</body>

</html>