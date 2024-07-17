<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medication</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <style>
    tr td button.btn {
        margin-left: 5px;
    }

    .modal-dialog.modal-wide {
        max-width: 40%;
    }
    </style>
</head>

<body>
    <div class="container mt-5">
        <div class="mb-3 mt-3">
            <a href="index.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i>
            </a>
        </div>
        <h1>Medication Dashboard</h1>
        <h5>Medication adalah sumber daya yang merepresentasikan informasi tentang obat-obatan. Ini mencakup detail
            tentang produk obat itu sendiri.</h5>
        <div class="mb-3 mt-3">
            <button class="btn btn-primary" data-toggle="modal" data-target="#addEncounterModal">Tambah
                Data</button>
        </div>

        <div id="medication">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Code</th>
                        <th>Nama Obat</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Data akan diisi oleh JavaScript -->
                </tbody>
            </table>
        </div>

        <!-- Add Encounter Modal -->
        <div class="modal fade" id="addEncounterModal" tabindex="-1" aria-labelledby="addEncounterModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addEncounterModalLabel">Tambah Data</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="addEncounterForm">
                            <div class="form-group">
                                <label for="codeObat">Code Obat</label>
                                <input type="number" class="form-control" id="codeObat" name="codeObat" required>
                                <div class="invalid-feedback">
                                    Kode obat harus diisi dan berupa angka.
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="namaObat">Nama Obat</label>
                                <input type="text" class="form-control" id="namaObat" name="namaObat" required>
                                <div class="invalid-feedback">
                                    Nama obat harus diisi dan tidak boleh mengandung karakter khusus.
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal read data -->
        <div class="modal fade" id="infoModal" tabindex="-1" aria-labelledby="infoModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-wide">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="infoModalLabel">Detail Obat</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p><strong>ID Obat:</strong> <span id="modalIdObat"></span></p>
                        <p><strong>Code Obat:</strong> <span id="modalCodeObat"></span></p>
                        <p><strong>Nama Obat:</strong> <span id="modalNamaObat"></span></p>
                        <p><strong>Status:</strong> <span id="modalStatus"></span></p>
                        <p><strong>Jenis Obat:</strong> <span id="modalJenisObat"></span></p>
                        <p><strong>ID Manufaktur:</strong> <span id="modalIdPembuat"></span></p>
                        <p><strong>Update Terakhir:</strong> <span id="modalLastUpdated"></span></p>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <div id="loading" style="display:none;">Loading&#8230;</div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="js/formHandler.js"></script>
    <script>
    postMedication("#addEncounterForm", "api/payload/post-medication-obat.php");
    readMedication("get-data/medication-obat.php");
    </script>
</body>

</html>