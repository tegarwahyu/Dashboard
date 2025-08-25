<!-- Modal Edit SRDR -->
<div class="modal fade" id="editAktualModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form id="formEditAktual" method="POST">
        @csrf
        @method('PUT')

        <div class="modal-header">
          <h5 class="modal-title">Edit Data SRDR</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body row g-3">
          <div class="col-md-6">
            <label>Sales Number</label>
            <input type="text" class="form-control" name="sales_number" id="edit-sales_number">
          </div>

          <div class="col-md-6">
            <label>Branch</label>
            <input type="text" class="form-control" name="branch" id="edit-branch">
          </div>

          <div class="col-md-6">
            <label>Brand</label>
            <input type="text" class="form-control" name="brand" id="edit-brand">
          </div>

          <div class="col-md-6">
            <label>City</label>
            <input type="text" class="form-control" name="city" id="edit-city">
          </div>

          <div class="col-md-6">
            <label>Visit Purpose</label>
            <input type="text" class="form-control" name="visit_purpose" id="edit-visit_purpose">
          </div>

          <div class="col-md-6">
            <label>Payment Method</label>
            <input type="text" class="form-control" name="payment_method" id="edit-payment_method">
          </div>

          <div class="col-md-6">
            <label>Menu Category</label>
            <input type="text" class="form-control" name="menu_category" id="edit-menu_category">
          </div>

          <div class="col-md-6">
            <label>Menu Category Detail</label>
            <input type="text" class="form-control" name="menu_category_detail" id="edit-menu_category_detail">
          </div>

          <div class="col-md-6">
            <label>Menu</label>
            <input type="text" class="form-control" name="menu" id="edit-menu">
          </div>

          <div class="col-md-6">
            <label>Menu Code</label>
            <input type="text" class="form-control" name="menu_code" id="edit-menu_code">
          </div>

          <div class="col-md-6">
            <label>Order Mode</label>
            <input type="text" class="form-control" name="order_mode" id="edit-order_mode">
          </div>

          <div class="col-md-6">
            <label>Qty</label>
            <input type="text" class="form-control" name="qty" id="edit-qty">
          </div>

          <div class="col-md-6">
            <label>Price</label>
            <input type="number" class="form-control" name="price" id="edit-price">
          </div>

          <div class="col-md-6">
            <label>Subtotal</label>
            <input type="number" class="form-control" name="subtotal" id="edit-subtotal">
          </div>

          <div class="col-md-6">
            <label>Discount</label>
            <input type="number" class="form-control" name="discount" id="edit-discount">
          </div>

          <div class="col-md-6">
            <label>Total</label>
            <input type="number" class="form-control" name="total" id="edit-total">
          </div>

          <div class="col-md-6">
            <label>Nett Sales</label>
            <input type="number" class="form-control" name="nett_sales" id="edit-nett_sales">
          </div>

          <div class="col-md-6">
            <label>Bill Discount</label>
            <input type="number" class="form-control" name="bill_discount" id="edit-bill_discount">
          </div>

          <div class="col-md-6">
            <label>Total After Bill Discount</label>
            <input type="number" class="form-control" name="total_after_bill_discount" id="edit-total_after_bill_discount">
          </div>
          
          <div class="col-md-6">
            <label>Waiters</label>
            <input type="text" class="form-control" name="waiters" id="edit-waiters">
          </div>

          <!-- Tambah kolom lain sesuai kebutuhan -->
        </div>

        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        </div>
      </form>
    </div>
  </div>
</div>