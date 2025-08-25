<!-- Modal Edit -->
<div class="modal fade" id="editBrandModal" tabindex="-1" role="dialog" aria-labelledby="editBrandModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <form method="POST" action="{{ route('brand.update') }}" enctype="multipart/form-data">
      @csrf

      <input type="hidden" name="id" id="edit_id">

      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Edit Logo Brand</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <div class="modal-body">
          <div class="row">
            <!-- Kolom Nama Brand -->
            <div class="col-md-6">
              <div class="form-group">
                <label>Nama Brand</label>
                <input type="text" name="nama_brand" id="edit_nama_brand" class="form-control" required>
              </div>
            </div>

            <!-- Kolom Logo Brand -->
            <div class="col-md-6">
              <div class="form-group">
                <label>Logo Brand</label>
                <input type="file" name="logo_brand" class="form-control" accept="image/*" onchange="previewEditLogo(event)">
              </div>
            </div>
          </div>

          <!-- Baris Bawah: Logo Saat Ini & Preview Logo Baru -->
          <div class="row mt-3">
            <div class="col-md-6">
              <label>Logo Saat Ini:</label><br>
              <img id="edit_logo_brand" src="#" alt="Logo Lama" style="max-height: 150px;">
            </div>
            <div class="col-md-6">
              <label>Preview Logo Baru:</label><br>
              <img id="logo-preview-edit" src="#" style="max-height: 150px; display: none;">
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <button type="submit" class="btn btn-success">Simpan Perubahan</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
        </div>
      </div>
    </form>
  </div>
</div>

<script>
  function previewEditLogo(event) {
    const input = event.target;
    const preview = document.getElementById('logo-preview-edit');
    if (input.files && input.files[0]) {
      const reader = new FileReader();
      reader.onload = function(e) {
        preview.src = e.target.result;
        preview.style.display = 'block';
      };
      reader.readAsDataURL(input.files[0]);
    }
  }
</script>