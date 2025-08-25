<div class="modal fade" id="outletModal" tabindex="-1" aria-labelledby="outletModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      
      <form action="{{ route('outlet_store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="modal-header bg-success text-white">
          <h5 class="modal-title" id="outletModalLabel">Tambah Data Outlet</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
        </div>

        <div class="modal-body">
          <div class="row mb-3">
            <!-- Kode Outlet -->
            <div class="col-md-6">
              <div class="form-group">
                <label for="kode_outlet">Kode Outlet</label>
                <input type="text" name="kode_outlet" id="kode_outlet" class="form-control" required>
              </div>
            </div>

            <!-- Nama Outlet -->
            <div class="col-md-6">
              <div class="form-group">
                <label for="nama_outlet">Nama Outlet</label>
                <input type="text" name="nama_outlet" id="nama_outlet" class="form-control" required>
              </div>
            </div>
          </div>

          <div class="row mb-3">
            <!-- Lokasi Outlet -->
            <div class="col-md-6">
              <div class="form-group">
                <label for="lokasi">Lokasi Outlet</label>
                <textarea style="width: 370px;height: 150px;" type="text" name="lokasi" id="lokasi" class="form-control" required></textarea>
              </div>
            </div>

            <!-- Brand -->
            <div class="col-md-6">
              <div class="form-group">
                <label for="brand_id">Brand</label>
                <select name="brand_id" id="brand_id" class="form-select" required>
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
