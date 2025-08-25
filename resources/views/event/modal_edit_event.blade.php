<div class="modal fade" id="eventeditPromoModal" tabindex="-1" aria-labelledby="eventeditPromoModalModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      
      <form id="eventEditPromoForm" action="#" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="id" id="id">
        <input type="hidden" name="promosi_kpi_id" id="promosi_kpi_id">
        <input type="hidden" name="img_path" id="img_path_edit">
        <div class="modal-header bg-success text-white">
          <h5 class="modal-title" id="eventPromoModalLabel2">Edit Promosi</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
        </div>

        <div class="modal-body">
          <div class="row mb-3">
            <div class="col-md-6">
              <div class="form-group">
                <label for="jenis_event2" class="form-label">Jenis Promosi</label>
                <select name="jenis_event" id="jenis_event2" class="form-select" required>
                  <option value="">-- Pilih Jenis --</option>
                  <option value="Diskon">Diskon</option>
                  <option value="Voucher">Voucher</option>
                  <option value="Bundling Menu">Bundling Menu</option>
                  <option value="Live Event">Live Event</option>
                </select>
              </div>
            </div>

            <div class="col-md-6">
              <div class="form-group">
                <label for="judul2" class="form-label">Judul Promosi</label>
                <input type="text" name="judul" id="judul2" class="form-control" placeholder="Contoh: Promo Makan Hemat 50%" required>
              </div>
            </div>
          </div>

          <div class="row mb-3">

            <div class="col-md-3">
              <div class="form-group">
                <label for="poster2" class="form-label">Gambar Poster</label>
                <input class="form-control" type="file" name="poster" id="poster2" accept="image/*" onchange="previewEditLogo(event)" required>
                </br>
              </div>
            </div>

            <div class="col-md-3">
              <div class="form-group">
                <label>Preview Poster Promo:</label><br>
                <img id="logo-preview-edit2" src="#" style="max-height: 150px; padding-left: 45px; display: none;">
              </div>
            </div>

            <div class="col-md-6">
              <div class="form-group">
                <label for="deskripsi2" class="form-label">Deskripsi Promosi</label>
                <textarea name="deskripsi" id="deskripsi2" rows="3" class="form-control" placeholder="Jelaskan detail promosi..." required></textarea>
              </div>
            </div>

          </div>

          <div class="row mb-3">

            <!-- Select Brand -->
            <div class="col-md-6">
              <div class="form-group">
                <label for="brand_id2" class="form-label">Brand</label>
                <select name="brand_id" id="brand_id2" class="form-select" required>
                  @foreach($data_outlet as $brand)
                    <option value="{{ $brand->id }}">{{ $brand->nama_brand }}</option>
                  @endforeach
                </select>
              </div>
            </div>
            
            <div class="col-md-6">
              <div id="outletContainer2">
                <!-- Akan di-replace -->
              </div>
            </div>

          </div>

          <div class="row mb-3">
            <!-- Tambahkan container khusus untuk dynamic outlet + date -->
            <div class="col-md-12">
              <div id="outletDateFieldsContainer2">
                <!-- Field date per outlet akan di-generate di sini -->
              </div>
            </div>

            <input type="hidden" name="outletDateTimeField2" id="outletDateTimeField2">
          
          </div>
          
        </div>
        
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" id="submitButton2" class="btn btn-success">Simpan Promosi</button>
        </div>
        
      </form>

    </div>
  </div>
</div>

