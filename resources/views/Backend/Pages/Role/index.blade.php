@extends('Backend.Layout.App')
@section('title', ' Role Management List | Admin Panel')
@section('style')
@endsection
@section('content')
    <div class="row">
        <div class="col-md-12 ">
            <div class="card">
                <div class="card-body">
                    <button data-toggle="modal" data-target="#addModal" type="button" class=" btn btn-success mb-2"><i class="mdi mdi-account-plus"></i> Add New Role</button>

                    <div class="table-responsive" id="tableStyle">
                        <table id="role_datatable" class="table table-bordered dt-responsive nowrap"
                            style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Role Name</th>
                                    <th>Permission</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog " role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ModalLabel"><i class="mdi mdi-account-check"></i> &nbsp; Create Role</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{route('admin.user.role.store')}}" method="POST" id="add_roll_form">
            @csrf
            <div class="card-body">
                <div class="form-group">
                    <label for="role_name">Role Name</label>
                    <input type="text" name="name" class="form-control" placeholder="Enter role name" required>
                </div>

                <div class="form-group">
                    <label>Assign Permissions</label>
                    <div class="row">
                        @foreach(\Spatie\Permission\Models\Permission::where('guard_name', 'admin')->get() as $permission)
                            <div class="col-md-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $permission->name }}" id="perm_{{ $permission->id }}">
                                <label class="form-check-label" for="perm_{{ $permission->id }}">
                                {{ $permission->name }}
                                </label>
                            </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <button type="button" onclick="history.back();" class="btn btn-danger">Back</button>
                <button type="submit" class="btn btn-primary">Create Role</button>
            </div>
        </form>
        </div>
    </div>
</div>
    @include('Backend.Modal.delete_modal')


@endsection

@section('script')
    <script src="{{ asset('Backend/assets/js/__handle_submit.js') }}"></script>
    <script src="{{ asset('Backend/assets/js/delete_data.js') }}"></script>

    <script type="text/javascript">
        $(document).ready(function() {
            handleSubmit('#add_roll_form', '#addModal');
            handleSubmit('#edit_roll_form', '#editModal');


        });








        /** Handle Edit button click **/
        $('#datatable1 tbody').on('click', '.edit-btn', function() {
            var id = $(this).data('id');
            $.ajax({
                url: "{{ route('admin.pop.edit', ':id') }}".replace(':id', id),
                method: 'GET',
                success: function(response) {
                    if (response.success) {


                        $('#popEditForm input[name="id"]').val(response.data.id);
                        $('#popEditForm input[name="name"]').val(response.data.name);
                        $('#popEditForm input[name="username"]').val(response.data.username);

                        $('#popEditForm input[name="password"]')
                        .val(response.data.password)
                        .prop('readonly', true);

                        $('#popEditForm input[name="phone"]').val(response.data.phone);
                        $('#popEditForm input[name="email"]').val(response.data.email);
                        $('#popEditForm input[name="address"]').val(response.data.address);
                        $('#popEditForm select[name="status"]').val(response.data.status).trigger('change');

                        // Show the modal
                        $('#editPopBranchModal').modal('show');
                    } else {
                        toastr.error('Failed to fetch Supplier data.');
                    }
                },
                error: function() {
                    toastr.error('An error occurred. Please try again.');
                }
            });
        });

        /** Handle Delete button click**/
        $('#datatable1 tbody').on('click', '.delete-btn', function() {
            var id = $(this).data('id');
            var deleteUrl = "{{ route('admin.pop.delete', ':id') }}".replace(':id', id);

            $('#deleteForm').attr('action', deleteUrl);
            $('#deleteModal').find('input[name="id"]').val(id);
            $('#deleteModal').modal('show');
        });
        /*Handle Pop Branch Login */
        $("#datatable1 tbody").on('click', 'button[name="pop_login_button"]',function(){
            var $button = $(this);
            var id = $button.data('id');

            /* Show spinner*/
            $button.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Logging in...');
            $button.prop('disabled', true);

            var login_url = "{{ route('admin.pop.branch.auto_login', ':id') }}".replace(':id', id);
            setTimeout(function () {
                //window.location.href = login_url;
            }, 500);
        });
    </script>
@endsection
