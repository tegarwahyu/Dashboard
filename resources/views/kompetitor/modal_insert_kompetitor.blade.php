<!-- Modal -->
<div class="modal fade" id="modalCreateCompetitor" tabindex="-1" role="dialog" aria-labelledby="modalTitle" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <form id="formCreateCompetitor" action="/competitor/store" method="POST">
      <!-- CSRF Token -->
      <input type="hidden" name="_token" value="{{ csrf_token() }}">

        <div class="modal-content">
            <div class="modal-header">
            <h5 class="modal-title" id="modalTitle">Tambah Data Kompetitor</h5>
            <button type="button" id="closeKompetitorModal" class="close" data-dismiss="modal" aria-label="Close">
                <span>&times;</span>
            </button>
            </div>

            <div class="modal-body">
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="nama_outlet">Nama Outlet</label>
                        <input type="text" name="nama_outlet" id="nama_outlet" class="form-control" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="lokasi">Lokasi</label>
                        <textarea type="text" name="lokasi" id="lokasi" rows="3" class="form-control" required></textarea>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="kapasitas_outlet">Kapasitas Outlet</label>
                        <input type="number" name="kapasitas_outlet" id="kapasitas_outlet" class="form-control" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="waktu_visit">Waktu Visit</label>
                        <input type="datetime-local" name="waktu_visit" id="waktu_visit" class="form-control" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="estimasi_pengunjung">Estimasi Pengunjung</label>
                        <input type="number" name="estimasi_pengunjung" id="estimasi_pengunjung" class="form-control" required>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
            <button type="submit" class="btn btn-success">Simpan</button>
            <button type="button" class="btn btn-secondary btn-cancel" data-dismiss="modal">Batal</button>
            </div>
        </div>
    </form>
  </div>
</div>
