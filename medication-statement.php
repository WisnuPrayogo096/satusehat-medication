<?php
include 'get-data/koneksi.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medication Statement</title>
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
        <h1>Medication Statement Dashboard</h1>
        <h5>Medication Statement adalah sumber daya yang mencatat penggunaan obat oleh pasien, baik yang diresepkan oleh
            praktisi kesehatan maupun yang digunakan sendiri oleh pasien.</h5>

        <div id="medicationStatement">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>ID Encounter</th>
                        <th>Nama Pasien</th>
                        <th>ID Med Req</th>
                        <th>Obat yg digunakan</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "SELECT md.id_encounter, md.id_med_req, md.status, 
                                   po.nama_pasien, po.nama_obat, po.instruksi_dosis, po.instruksi_pasien 
                            FROM medication_dispense md
                            JOIN pesanan_obat po ON md.id_encounter = po.id_encounter AND md.id_med_req = po.id_med_req";
                    $result = $conn->query($sql);
                    if ($result->num_rows > 0) {
                        $no = 1;
                        while($row = $result->fetch_assoc()) {
                            $statusClass = $row['status'] == 'dihentikan' ? 'text-danger' : 'text-primary';
                            echo "<tr>
                                    <td>{$no}</td>
                                    <td>{$row['id_encounter']}</td>
                                    <td>{$row['nama_pasien']}</td>
                                    <td>{$row['id_med_req']}</td>
                                    <td>{$row['nama_obat']}</td>
                                    <td class='{$statusClass} font-weight-bold'>{$row['status']}</td>
                                    <td>
                                        <button class='btn btn-primary' data-toggle='modal' data-target='#catatanModal' data-encounter='{$row['id_encounter']}' data-medreq='{$row['id_med_req']}' data-status='{$row['status']}' data-dosis='{$row['instruksi_dosis']}' data-medis='{$row['instruksi_pasien']}'>Update Catatan</button>
                                    </td>
                                  </tr>";
                            $no++;
                        }
                    } else {
                        echo "<tr><td colspan='7'>No data available</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <!-- Add Modal -->
        <div class="modal fade" id="catatanModal" tabindex="-1" aria-labelledby="catatanModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="catatanModalLabel">Update Catatan</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="catatanObat" action="get-data/update_med_state.php" method="post">
                            <input type="hidden" id="modal_id_encounter" name="id_encounter">
                            <input type="hidden" id="modal_id_med_req" name="id_med_req">
                            <div class="form-group">
                                <label for="status_penggunaan">Status Penggunaan</label>
                                <select class="form-control" id="status_penggunaan" name="status_penggunaan" required>
                                    <option value="" selected disabled>Pilih status</option>
                                    <option value="sedang digunakan">Sedang Digunakan</option>
                                    <option value="dihentikan">Dihentikan</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="dosis">Instruksi Dosis</label>
                                <textarea type="text" class="form-control" id="dosis" name="dosis" rows="3"></textarea>
                            </div>
                            <div class="form-group">
                                <label for="medis">Catatan Medis</label>
                                <textarea type="text" class="form-control" id="medis" name="medis" rows="3"></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
    $('#catatanModal').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget);
        var id_encounter = button.data('encounter');
        var id_med_req = button.data('medreq');
        var status = button.data('status');
        var dosis = button.data('dosis');
        var medis = button.data('medis');

        var modal = $(this);
        modal.find('#modal_id_encounter').val(id_encounter);
        modal.find('#modal_id_med_req').val(id_med_req);
        modal.find('#status_penggunaan').val(status);
        modal.find('#dosis').val(dosis);
        modal.find('#medis').val(medis);
    });

    $('#catatanObat').on('submit', function(event) {
        event.preventDefault();
        $.ajax({
            url: $(this).attr('action'),
            type: $(this).attr('method'),
            data: $(this).serialize(),
            success: function(response) {
                var data = JSON.parse(response);
                if (data.error) {
                    alert('Error: ' + data.error);
                } else {
                    alert('Request berhasil disimpan.');
                    location.reload();
                }
            },
            error: function(xhr, status, error) {
                alert("Error: " + xhr.responseText);
            }
        });
    });
    </script>

</body>

</html>

<?php
$conn->close();
?>