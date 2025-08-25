<!-- Modal Upload Brand -->
<div class="modal fade" id="eventTambahBrandModal" tabindex="-1" aria-labelledby="eventTambahBrandModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <!-- Modal Header -->
      <div class="modal-header">
        <h5 class="modal-title" id="eventTambahBrandModalLabel">Tambah Nama Brand</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
      </div>

      <!-- Modal Body -->
      <div class="modal-body">
        <form action="{{ route('brand_store') }}" method="POST" enctype="multipart/form-data">
          @csrf
          <div class="mb-3">
            <label for="nama_brand" class="form-label">Nama Brand</label>
            <input type="text" class="form-control" id="nama_brand" name="nama_brand" required>
          </div>
          <div class="mb-3">
            <label for="logo_brand" class="form-label">Logo Brand </label>
            <input class="form-control" type="file" name="logo_brand" id="logo_brand" accept="image/*" required onchange="validateFileSize(this)">
            <p style="font-size: 12px; margin-top: 4px;">Silakan upload gambar dengan rasio 1:1 atau minimal ukuran 800×800 piksel.</p>
            <p style="font-size: 12px; margin-top: 4px;">Silakan upload gambar dengan ukuran file 1.5 MB</p>
          </div>
          <button type="submit" class="btn btn-primary">Simpan</button>
          <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Batal</button>
        </form>
      </div>
    </div>
  </div>
</div>
