<div class="modal fade" id="userInsertModal" tabindex="-1" aria-labelledby="userInsertModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      
      
        <div class="modal-header bg-success text-white">
            <h5 class="modal-title" id="eventPromoModalLabel">Unggah Users</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
        </div>

        <form action="#" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                       <div class="form-group">
                            <label for="nama" class="form-label">Nama User</label>
                            <input type="text" name="nama" id="nama" class="form-control" placeholder="Nama users" required>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="email" class="form-label">Email</label>
                            <input type="text" name="email" id="email" class="form-control" placeholder="email@rockectmail.com" required>
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                         <div class="form-group">
                            <label for="role" class="form-label">Role</label>
                            <select name="role" id="role" class="form-select" required>
                            <option value="">-- Pilih Role --</option>
                            <option value="manager">Manager</option>
                            <!-- <option value="manager.supervisor">Supervisor</option> -->
                            <option value="pic">PIC</option>
                            <option value="marketing">Marketing</option>
                            <option value="aspv">Asisten Supervisor</option>
                            <option value="ma">Manager Area</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label for="lokasi" class="form-label">Lokasi</label>
                        <select id="lokasi" name="lokasi" class="form-control">
                            <option value="">-- Pilih Outlet --</option>
                        </select>
                    </div>

                </div>

                <div class="row mb-3">
                    <div class="col-md-6" id="password-group">
                        <div class="form-group">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" name="password" id="password" class="form-control" placeholder="*****************">
                        </div>
                    </div>
                    
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">Simpan Promosi</button>
                </div>
            </div>
        </form>

    </div>
  </div>
</div>