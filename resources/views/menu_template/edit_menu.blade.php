<!-- Modal Edit Aktual -->
<div class="modal fade" id="editMenuTemplateModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="formEditMenuTemplate" method="POST">
        @csrf
        @method('PUT')
        
        <div class="modal-header">
          <h5 class="modal-title">Edit Data Menu Template</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">
          <div class="mb-3">
            <label>Menu template name</label>
            <input type="text" class="form-control" name="menu_template_name" id="edit-menu-template">
          </div>

          <div class="mb-3">
            <label>Menu category</label>
            <input type="text" class="form-control" name="menu_category" id="edit-menu-category">
          </div>

          <div class="mb-3">
            <label>Menu category detail</label>
            <input type="text" class="form-control" name="menu_category_detail" id="edit-menu-category-detail">
          </div>

          <div class="mb-3">
            <label>Menu name</label>
            <input type="text" class="form-control" name="menu_name" id="edit-menu-name">
          </div>

          <div class="mb-3">
            <label>Menu short name</label>
            <input type="text" class="form-control" name="menu_short_name" id="edit-menu-short-name">
          </div>
          <div class="mb-3">
            <label>Menu code</label>
            <input type="text" class="form-control" name="menu_code" id="edit-menu-code">
          </div>
          <div class="mb-3">
            <label>Price</label>
            <input type="number" class="form-control" name="price" id="edit-price">
          </div>
          
          <div class="mb-3">
            <label>Status</label>
            <select name="status" id="edit-status" class="form-select" required>
              <option value="" selected disabled>-- Pilih Status --</option>
              <option value="ACTIVE">ACTIVE</option>
              <option value="NOT ACTIVE">NOT ACTIVE</option>
            </select>
          </div>
        </div>

        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        </div>
      </form>
    </div>
  </div>
</div>