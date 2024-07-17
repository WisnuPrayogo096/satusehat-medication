<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medication Dispense</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>

<body>
    <div class="container mt-5">
        <div class="mb-3 mt-3">
            <a href="index.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i>
            </a>
        </div>
        <h1>Medication Dispense Dashboard</h1>
        <h5>Medication Dispense adalah sumber daya yang merepresentasikan tindakan penyerahan obat kepada pasien,
            biasanya oleh apoteker.</h5>

        <div id="medicationDispense">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>ID Encounter</th>
                        <th>Nama Pasien</th>
                        <th>Obat</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    require 'get-data/koneksi.php';

                    $sql = "SELECT id, id_encounter, nama_pasien, nama_obat, status, id_medic, id_pasien, id_med_req, instruksi_dosis FROM pesanan_obat";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        $no = 1;
                        while ($row = $result->fetch_assoc()) {
                            $statusClass = '';
                            $disabledProcessed = '';
                            $disabledDispensed = '';

                            if ($row['status'] == 'order') {
                                $statusClass = 'text-danger font-weight-bold';
                                $disabledDispensed = 'disabled';
                            } elseif ($row['status'] == 'Process') {
                                $statusClass = 'text-warning font-weight-bold';
                                $disabledProcessed = 'disabled';
                            } elseif ($row['status'] == 'Done') {
                                $statusClass = 'text-success font-weight-bold';
                                $disabledProcessed = 'disabled';
                                $disabledDispensed = 'disabled';
                            }

                            echo "<tr>";
                            echo "<td>" . $no++ . "</td>";
                            echo "<td>" . $row["id_encounter"] . "</td>";
                            echo "<td>" . $row["nama_pasien"] . "</td>";
                            echo "<td>" . $row["nama_obat"] . "</td>";
                            echo "<td class='$statusClass'>" . $row["status"] . "</td>";
                            echo "<td>
                                    <button type='button' class='btn btn-warning process-btn' data-id='" . $row['id'] . "' $disabledProcessed>Diproses</button>
                                    <button type='button' class='btn btn-primary dispense-btn' 
                                        data-id='" . $row['id'] . "' 
                                        data-id-medic='" . $row['id_medic'] . "'
                                        data-display='" . $row['nama_obat'] . "'
                                        data-id-pasien='" . $row['id_pasien'] . "'
                                        data-nama-pasien='" . $row['nama_pasien'] . "'
                                        data-id-encounter='" . $row['id_encounter'] . "'
                                        data-id-med-req='" . $row['id_med_req'] . "'
                                        data-instruksi-dosis='" . $row['instruksi_dosis'] . "'
                                        $disabledDispensed>Diserahkan</button>
                                  </td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='6'>No data found</td></tr>";
                    }

                    $conn->close();
                    ?>
                </tbody>
            </table>

        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
    $(document).ready(function() {
        $('.process-btn').click(function() {
            var id = $(this).data('id');
            $.ajax({
                url: 'get-data/update_status_dispense.php',
                type: 'POST',
                data: JSON.stringify({
                    id: id,
                    status: 'Process'
                }),
                contentType: 'application/json',
                success: function(response) {
                    location.reload();
                }
            });
        });

        $('.dispense-btn').click(function() {
            var id = $(this).data('id');
            var idMedic = $(this).data('id-medic');
            var display = $(this).data('display');
            var idPasien = $(this).data('id-pasien');
            var namaPasien = $(this).data('nama-pasien');
            var idEncounter = $(this).data('id-encounter');
            var idMedReq = $(this).data('id-med-req');
            var instruksiDosis = $(this).data('instruksi-dosis');

            var data = {
                id: id,
                id_medic: idMedic,
                display: display,
                id_pasien: idPasien,
                nama_pasien: namaPasien,
                id_encounter: idEncounter,
                id_med_req: idMedReq,
                instruksi_dosis: instruksiDosis
            };

            $.ajax({
                url: 'get-data/update_status_dispense.php',
                type: 'POST',
                data: JSON.stringify(data),
                contentType: 'application/json',
                success: function(response) {
                    alert('Request berhasil disimpan. Response: ' + response);
                    location.reload();
                },
                error: function(xhr, status, error) {
                    alert("Error: " + xhr.responseText);
                }
            });
        });
    });
    </script>
</body>

</html>