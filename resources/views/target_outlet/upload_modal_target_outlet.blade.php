<!-- Modal -->
<div class="modal fade" id="targetOutletModal" tabindex="-1" aria-labelledby="targetOutletModalModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

      <form action="#" method="POST">
        @csrf
        <div class="modal-header bg-success text-white">
          <h5 class="modal-title" id="targetOutletModalModalLabel">Setting Target Outlet</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
        </div>

        <div class="modal-body">
          <!-- Outlet -->
          <div class="row mb-3">
            <div class="col-md-6">
              <label for="outlet_id">Pilih Outlet</label>
              <select name="outlet_id" id="outlet_id" class="form-select" required></select>
            </div>
          <!-- </div> -->

          <!-- Pilih Bulan -->
          <!-- <div class="row mb-3"> -->
            <div class="col-md-3">
              <label for="week_number">Pilih Minggu</label>
              <select name="week_number" id="week_number" class="form-select" required>
                <option value="" disabled selected>-- Pilih minggu --</option>
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="4">4</option>
                <option value="5">5</option>
              </select>
            </div>

            <div class="col-md-3">
              <label for="bulan">Pilih Bulan</label>
              <select name="bulan" id="bulan" class="form-select" required>
                <option value="" disabled selected>-- Pilih Bulan --</option>
                <option value="1">Januari</option>
                <option value="2">Februari</option>
                <option value="3">Maret</option>
                <option value="4">April</option>
                <option value="5">Mei</option>
                <option value="6">Juni</option>
                <option value="7">Juli</option>
                <option value="8">Agustus</option>
                <option value="9">September</option>
                <option value="10">Oktober</option>
                <option value="11">November</option>
                <option value="12">Desember</option>
              </select>
            </div>
          </div>

          <!-- Target Values -->
          <div class="row mb-3">
            <div class="col-md-3">
              <label for="senin">Senin</label>
              <input type="number" class="form-control" name="senin" id="senin" required>
            </div>
            <div class="col-md-3">
              <label for="selasa">Selasa</label>
              <input type="number" class="form-control" name="selasa" id="selasa" required>
            </div>
            <div class="col-md-3">
              <label for="rabu">Rabu</label>
              <input type="number" class="form-control" name="rabu" id="rabu" required>
            </div>
            <div class="col-md-3">
              <label for="kamis">Kamis</label>
              <input type="number" class="form-control" name="kamis" id="kamis" required>
            </div>
            <div class="col-md-3">
              <label for="jumat">Jum'at</label>
              <input type="number" class="form-control" name="jumat" id="jumat" required>
            </div>
            <div class="col-md-3">
              <label for="sabtu">Sabtu</label>
              <input type="number" class="form-control" name="sabtu" id="sabtu" required>
            </div>
            <div class="col-md-3">
              <label for="minggu">Minggu</label>
              <input type="number" class="form-control" name="minggu" id="minggu" required>
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-success">Simpan Target</button>
        </div>
      </form>

    </div>
  </div>
</div>

@push('scripts')

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const outletSelect = document.getElementById('outlet_id');
        const outletChoices = new Choices(outletSelect, {
            placeholderValue: 'Pilih Outlet',
            searchPlaceholderValue: 'Cari Outlet',
            removeItemButton: false,
            shouldSort: false
        });

        // Load data saat modal ditampilkan
        $('#targetOutletModal').on('show.bs.modal', function () {
            $.ajax({
                url: "{{ route('getDataOutlet') }}",
                type: "GET",
                dataType: "json",
                success: function (data) {
                    // Clear pilihan lama
                    outletChoices.clearChoices();

                    // Tambahkan kembali default option
                    outletChoices.setChoices([{
                        value: '',
                        label: '-- Pilih Outlet --',
                        selected: true,
                        disabled: true
                    }], 'value', 'label', false);

                    // Tambahkan data dari server
                    data.forEach(function (item) {
                        outletChoices.setChoices([{
                            value: item.id,
                            label: item.name
                        }], 'value', 'label', false);
                    });
                },
                error: function (xhr) {
                    console.error(xhr.responseText);
                }
            });
        });
    });

    
    $('#targetOutletModal form').on('submit', function(e){
        e.preventDefault(); // cegah reload

        let formData = {
            outlet_id: $('#outlet_id').val(),
            week_number: $('#week_number').val(),
            bulan: $('#bulan').val(),
            senin: $('#senin').val(),
            selasa: $('#selasa').val(),
            rabu: $('#rabu').val(),
            kamis: $('#kamis').val(),
            jumat: $('#jumat').val(),
            sabtu: $('#sabtu').val(),
            minggu: $('#minggu').val(),
            _token: "{{ csrf_token() }}"
        };

        $.ajax({
            url: "{{ route('target-outlet.store') }}",
            type: "POST",
            data: formData,
            success: function(response){
                alert(response.message);
                $('#targetOutletModal').modal('hide');
                // di sini bisa reload table atau update data tanpa reload full page
            },
            error: function(xhr){
                if(xhr.status === 422){
                    let errors = xhr.responseJSON.errors;
                    let message = '';
                    for (let field in errors) {
                        message += errors[field][0] + "\n";
                    }
                    alert(message);
                } else {
                    alert('Terjadi kesalahan server.');
                }
            }
        });
    });
</script>
@endpush