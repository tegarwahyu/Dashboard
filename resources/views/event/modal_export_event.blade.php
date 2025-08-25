

<div class="modal fade" id="eventExportModal" tabindex="-1" aria-labelledby="eventExportModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      
      

        <div class="modal-header bg-success text-white">
          <h5 class="modal-title" id="eventPromoModalLabel2">Export Promosi</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
        </div>

        <form id="exportForm" action="{{ route('promosi.export') }}" method="GET" target="_blank">
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Pilih Rentang Tanggal:</label>
                            <input type="text" class="form-control" id="dateRange" name="date_range" placeholder="Pilih rentang tanggal">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="export_judul_promosi" class="form-label">Judul Promosi</label>
                            <div id="export_judul_promosi_container">
                            <input type="text" name="export_judul_promosi" id="export_judul_promosi" class="form-control" placeholder="Contoh: Promo Makan Hemat 50%" required>
                            </div>
                        </div>
                    </div>
                </div>
            
            </div>
            
            <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-success">Export</button>
            </div>
        
        </form>

    </div>
  </div>
</div>

