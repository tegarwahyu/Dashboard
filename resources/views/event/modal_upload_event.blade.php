<div class="modal fade" id="eventPromoModal" tabindex="-1" aria-labelledby="eventPromoModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      
      <form action="{{ route('post_promosi') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="modal-header bg-success text-white">
          <h5 class="modal-title" id="eventPromoModalLabel">Unggah Promosi</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
        </div>

        <div class="modal-body">
          
          <div class="row mb-3">
            
            <div class="col-md-6">
              <div class="form-group">
                <label for="judul" class="form-label">Nama Promosi</label>
                <input type="text" name="judul" id="judul" class="form-control" placeholder="Silahkan isi nama promosi anda .. " required>
              </div>
            </div>

            <div class="col-md-3">
              <div class="form-group">
                <label for="brand_id" class="form-label">Brand</label>
                  <select name="brand_id" id="brand_id" class="form-select" required>
                    @foreach($data_outlet as $brand)
                      <option value="{{ $brand->id }}">{{ $brand->nama_brand }}</option>
                    @endforeach
                  </select>
              </div>
            </div>
            
            <div class="col-md-3">
              <div id="outletContainer">
                <!-- Akan di-replace -->
              </div>
              <p style="font-size: 9px; margin-top: 4px;font-style: italic;">Silakan memilih brand terlebih dahulu, sebelum outlet</p>
            </div>
            
          </div>

          <div class="row mb-3">
            <!-- MENU CODE -->
            <div class="col-md-6">
              <label for="menu_code">Menu Kode</label>
                <select name="menu_kode[]" id="menu_kode" class="form-select" multiple required>
                  <!-- opsi dari AJAX -->
                </select>
              <!-- <select name="menu_code[]" id="menu_code" class="form-select" multiple required></select> -->
            </div>

            <!-- MENU NAME -->
            <div class="col-md-6">
              <label for="menu_name">Nama Menu</label>
              <select name="menu_name[]" id="menu_name" class="form-select" multiple required></select>
            </div>
          </div>

          <div class="row mb-3">
            <div class="col-md-3">
              <label>Mulai Promosi:</label>
              <input type="date" id="startDate" name="mulaiPromosi" class="form-control outlet-mulai" required>
            </div>
            <div class="col-md-3">
              <label>Akhir Promosi:</label>
              <input type="date" id="endDate" name="akhirPromosi" class="form-control outlet-akhir" required>
            </div>
            

            <div class="col-md-3">
              <div class="form-group">
                <label for="unit_type" class="form-label">Target Sales</label>
                  <!-- <select name="unit_type" id="target_type" class="form-select" required>
                    <option value="qty">Qty</option>
                    <option value="rupiah">Rupiah</option>
                  </select> -->
                  <select name="unit_type[]" id="target_type" class="form-select" multiple required>
                    <option value="qty">Qty</option>
                    <option value="rupiah">Rupiah</option>
                  </select>
              </div>
            </div>

            

          </div>
          <div class="row mb-3">
            <!-- Container untuk input target sales -->
            <div class="row mt-3" id="target_sales_container"></div>

            
          </div>
          



          <div class="row mb-3">

            <div class="col-md-3">
              <div class="form-group">
                <label for="poster" class="form-label">Gambar Poster</label>
                <input class="form-control" type="file" name="poster" id="poster" accept="image/*" onchange="previewEditLogo(event)" required>
                </br>
                
              </div>
            </div>

            <div class="col-md-3">
              <div class="form-group">
                <label>Preview Poster Promo:</label><br>
                <img id="logo-preview-edit" src="#" style="max-height: 150px; padding-left: 45px; display: none;">
              </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                  <label for="deskripsi" class="form-label">Deskripsi Promosi</label>
                  <textarea name="deskripsi" id="deskripsi" rows="3" class="form-control" placeholder="Jelaskan detail promosi..." required></textarea>
                </div>
            </div>

          </div>


          <div class="row mb-3">
              <!-- Tambahkan container khusus untuk dynamic outlet + date -->
              <div class="col-md-6">
              <div class="form-group">
                <label for="budget_marketing" class="form-label">Budget Marketing</label>
                <input type="number" name="budget_marketing" id="budget_marketing" class="form-control" placeholder="Silahkan isi Budget Marketing " required>
              </div>
            </div>
          
          </div>
          
        </div>
        
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-success">Simpan Promosi</button>
        </div>
        
      </form>

    </div>
  </div>
</div>

<!-- Panggil library -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css">
<script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    const menuCodeSelect = document.getElementById('menu_kode');
    const menuNameSelect = document.getElementById('menu_name');

    let menuMap = []; // Menyimpan pasangan menu_code <=> menu_name
    let menuCodeChoices;
    let menuNameChoices;

    fetch('/promosiAPI/menu-template')
      .then(response => response.json())
      .then(data => {
        menuMap = data;

        // Siapkan data choices
        const codeChoices = data.map(item => ({
          value: item.menu_code,
          label: item.menu_code
        }));

        const nameChoices = data.map(item => ({
          value: item.menu_name,
          label: item.menu_name
        }));

        // Inisialisasi Choices.js untuk Menu Code
        menuCodeChoices = new Choices(menuCodeSelect, {
          removeItemButton: true,
          placeholderValue: 'Pilih Menu Code',
          searchPlaceholderValue: 'Cari Menu Code',
          choices: codeChoices
        });

        // Inisialisasi Choices.js untuk Menu Name
        menuNameChoices = new Choices(menuNameSelect, {
          removeItemButton: true,
          placeholderValue: 'Pilih Menu Name',
          searchPlaceholderValue: 'Cari Menu Name',
          choices: nameChoices
        });

        // Event sinkronisasi
        menuCodeSelect.addEventListener('change', syncFromCode);
        menuNameSelect.addEventListener('change', syncFromName);
      })
      .catch(error => {
        console.error('Gagal mengambil data menu:', error);
      });

    function syncFromCode() {
      const selectedCodes = menuCodeChoices.getValue(true); // array of selected code values
      const matchedNames = menuMap
        .filter(item => selectedCodes.includes(item.menu_code))
        .map(item => item.menu_name);

      menuNameChoices.removeActiveItems();
      matchedNames.forEach(name => {
        menuNameChoices.setChoiceByValue(name);
      });
    }

    function syncFromName() {
      const selectedNames = menuNameChoices.getValue(true); // array of selected name values
      const matchedCodes = menuMap
        .filter(item => selectedNames.includes(item.menu_name))
        .map(item => item.menu_code);

      menuCodeChoices.removeActiveItems();
      matchedCodes.forEach(code => {
        menuCodeChoices.setChoiceByValue(code);
      });
    }
  });
</script>



<script>
  const brandSelect = new Choices('#brand_id', {
    removeItemButton: true,
    placeholderValue: 'Pilih Brand',
    searchPlaceholderValue: 'Cari Brand'
  });

  new Choices('#target_type', {
    removeItemButton: true,
    placeholderValue: 'Pilih Target Sales',
    searchPlaceholderValue: 'Cari Target'
  });

</script>

<script>
  document.addEventListener('DOMContentLoaded', function() {

    let lastOutletData = [];
    // 1) Setup Choices untuk brand (single)
    const brandSelect = new Choices('#brand_id', {
      removeItemButton: false,
      placeholderValue: 'Pilih Brand',
      searchPlaceholderValue: 'Cari Brand'
    });

    // 3) Load Outlet ketika brand dipilih
    $('#brand_id').on('change', function() {
      
      var brandID = $(this).val();
      if (brandID) {
        $.ajax({
          url: '/get-outlet/' + brandID,
          type: 'GET',
          dataType: 'json',
          success: function(data) {
            // console.log('✅ Outlet Response:', data);

            lastOutletData = data;

            const html = renderSelect('outlet_id', data);
            $('#outletContainer').html(html);

            // Re-initialize Choices.js untuk outlet yang baru
            new Choices('#outlet_id', {
              removeItemButton: true,
              placeholderValue: 'Pilih Outlet',
              searchPlaceholderValue: 'Cari Outlet'
            });

          },
          error: function(xhr, status, error) {
            console.log('AJAX Error:', error);
          }
        });
      } else {
        $('#outlet_id').empty();
      }
    });

    function createOptions(opt) {
      // return `<option value="${opt.id}">${opt.nama_outlet}</option>`;
      // console.log(opt.outlet)
        if(opt.outlet === undefined){
            return `<option value="${opt.id}">${opt.nama_outlet}</option>`;
        }else {
            return `<option value="${opt.outlet.id}">${opt.outlet.nama_outlet}</option>`;
        }
      // return `<option value="${opt.outlet.id}">${opt.outlet.nama_outlet}</option>`;
    }

    function renderSelect(name, dataArray) {
      const options = dataArray.map(opt => createOptions(opt)).join('');
      return `
        <div class="form-group">
          <label for="${name}" class="form-label">Outlet</label>
          <select name="${name}[]" id="${name}" class="form-select" multiple required>
            ${options}
          </select>
        </div>
      `;
    }


  });

  // Fungsi auto resize textarea
  function autoResize(textarea) {
    textarea.style.height = 'auto';
    textarea.style.height = textarea.scrollHeight + 'px';
  }

  // Fungsi preview poster
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

<script>
  function generateOutletDateTimeArray() {
    const array = [];

    $('#outletDateFieldsContainer .row').each(function() {
      const outletId = $(this).find('.outlet-mulai').data('outlet-id');
      const mulai = $(this).find('.outlet-mulai').val();
      const akhir = $(this).find('.outlet-akhir').val();
      const traffic = $(this).find('.traffic-input').val();
      const pax = $(this).find('.pax-input').val();
      const bill = $(this).find('.bill-input').val();
      const budget = $(this).find('.budget-input').val();
      const sales = $(this).find('.sales-input').val();

      if (mulai && akhir) {
        array.push({
          outlet_id: parseInt(outletId),
          mulai_promosi: mulai,
          tanggal_akhir: akhir,
          traffic: parseInt(traffic) || 0,
          pax: parseInt(pax) || 0,
          bill: parseInt(bill) || 0,
          budget: parseInt(budget) || 0,
          sales: parseInt(sales) || 0
        });
      }
    });

    console.log('✅ Final array:', array);
    $('#outletDateTimeField').val(JSON.stringify(array));
  }
</script>

<script>
  const today = new Date();
  const yyyy = today.getFullYear();
  const mm = String(today.getMonth() + 1).padStart(2, '0'); // Ensure two digits
  const dd = String(today.getDate()).padStart(2, '0');      // Ensure two digits
  const minDate = `${yyyy}-${mm}-${dd}`;

  // Set minimum date to today
  document.getElementById("startDate").min = minDate;
  document.getElementById('startDate').addEventListener('change', function () {
    const selectedStart = this.value;
    const endDateInput = document.getElementById('endDate');
    endDateInput.min = selectedStart;
  });
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const menuCodeSelect = document.getElementById('menu_kode');
  const menuNameSelect = document.getElementById('menu_name');
  const targetTypeSelect = document.getElementById('target_type');
  const targetSalesContainer = document.getElementById('target_sales_container');

  // Cek jika elemen tidak ditemukan, tampilkan error di console
  if (!menuCodeSelect || !targetTypeSelect || !targetSalesContainer) {
    console.error('Salah satu elemen tidak ditemukan: periksa ID menu_kode, target_type, atau target_sales_container.');
    return;
  }

  function renderTargetInputs() {
    const selectedMenuCodes = Array.from(menuNameSelect.selectedOptions).map(opt => opt.value);
    const selectedTargetTypes = Array.from(targetTypeSelect.selectedOptions).map(opt => opt.value);
    console.log(selectedTargetTypes)
    targetSalesContainer.innerHTML = '';

    if (selectedMenuCodes.length === 0 || selectedTargetTypes.length === 0) return;

    selectedMenuCodes.forEach(code => {
      selectedTargetTypes.forEach(targetType => {
        const formGroup = document.createElement('div');
        formGroup.className = 'col-md-3 mb-3';

        formGroup.innerHTML = `
          <label>Target ${targetType.toUpperCase()} untuk Menu <strong>${code}</strong></label>
          <input 
            type="number" 
            class="form-control"
            name="target_sales[${code}][${targetType}]"
            placeholder="Target ${targetType}"
            required
          />
        `;

        targetSalesContainer.appendChild(formGroup);
      });
    });
  }

  // Pasang event listener
  menuCodeSelect.addEventListener('change', renderTargetInputs);
  targetTypeSelect.addEventListener('change', renderTargetInputs);
});
</script>

