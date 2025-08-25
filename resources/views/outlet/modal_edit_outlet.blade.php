<div class="modal fade" id="editOutletModal" tabindex="-1" aria-labelledby="editOutletModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      
      <form id="editOutletForm" method="POST" enctype="multipart/form-data">
      @csrf
      @method('PUT')

        <input type="hidden" name="id" id="edit_id">

        <div class="modal-header bg-success text-white">
          <h5 class="modal-title" id="outletModalLabel">Edit Data Outlet</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
        </div>

        <div class="modal-body">
          <div class="row mb-3">
            <!-- Kode Outlet -->
            <div class="col-md-6">
              <div class="form-group">
                <label for="kode_outlet">Kode Outlet</label>
                <input type="text" name="kode_outlet" id="edit_kode_outlet" class="form-control" required>
              </div>
            </div>

            <!-- Nama Outlet -->
            <div class="col-md-6">
              <div class="form-group">
                <label for="nama_outlet">Nama Outlet</label>
                <input type="text" name="nama_outlet" id="edit_nama_outlet" class="form-control" required>
              </div>
            </div>
          </div>

          <div class="row mb-3">
            <!-- Lokasi Outlet -->
            <div class="col-md-6">
              <div class="form-group">
                <label for="lokasi">Lokasi Outlet</label>
                <textarea style="width: 370px;height: 150px;" type="text" name="lokasi" id="edit_lokasi" class="form-control" required></textarea>
              </div>
            </div>

            <!-- Brand -->
            <div class="col-md-6">
              <div class="form-group">
                <label for="brand_id">Brand</label>
                <select name="brand_id" id="edit_brand_id" class="form-select" required>
                  <option value="" disabled selected>-- Pilih Brand --</option>
                  @foreach($data_brand as $brand)
                    <option value="{{ $brand->id }}">{{ $brand->nama_brand }}</option>
                  @endforeach
                </select>
              </div>
            </div>

          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-success">Simpan Data Outlet</button>
        </div>
      </form>

    </div>
  </div>
</div>
