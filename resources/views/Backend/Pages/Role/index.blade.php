@php
    use Spatie\Permission\Models\Role;
@endphp
@extends('Backend.Layout.App')
@section('title', ' Role Management List | Admin Panel')
@section('style')
    <style>
        .form-check {
            background: #f9f9f9;
            border: 1px solid #ddd;
            padding: 6px 10px;
            border-radius: 5px;
        }
    </style>

@endsection
@section('content')
    <div class="row">
        <div class="col-md-12 ">
            <div class="card">
                <div class="card-body">
                    <button data-toggle="modal" data-target="#addModal" type="button" class=" btn btn-success mb-2"><i
                            class="mdi mdi-account-plus"></i> Add New Role</button>

                    <div class="table-responsive" id="tableStyle">
                        <table id="role_datatable" class="table table-bordered dt-responsive nowrap"
                            style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Role Name</th>
                                    <th>Permission</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach (Role::with('permissions')->get() as $role)
                                    <tr>
                                        <td>{{ $role->id }}</td>
                                        <td>{{ $role->name }}</td>
                                        <td>
                                            @php
                                                $colorMap = [
                                                    'view' => 'info',
                                                    'edit' => 'warning',
                                                    'delete' => 'danger',
                                                    'create' => 'success',
                                                ];
                                            @endphp
                                            @foreach ($role->permissions as $perm)
                                                @php
                                                    $name = strtolower($perm->name);
                                                    $color = 'secondary'; // default
                                                    foreach ($colorMap as $key => $clr) {
                                                        if (strpos($name, $key) !== false) {
                                                            $color = $clr;
                                                            break;
                                                        }
                                                    }
                                                @endphp
                                                <span class="badge badge-{{ $color }}">{{ $perm->name }}</span>
                                            @endforeach
                                        </td>
                                        <td>

                                            <button class="btn btn-sm btn-danger delete-btn" data-id={{ $role->id }}
                                                type="submit">
                                                <i class="fas fa-trash-alt"></i> Delete
                                            </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="ModalLabel">
                        <i class="mdi mdi-account-check"></i> &nbsp; Create Role
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <form action="{{ route('admin.user.role.store') }}" method="POST" id="add_roll_form">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label><strong>Role Name</strong></label>
                            <input type="text" name="name" class="form-control" placeholder="Enter role name"
                                required>
                        </div>

                        @php
                            $permissions = \Spatie\Permission\Models\Permission::where('guard_name', 'admin')->get();

                            $grouped = collect([
                                'Menu Access' => $permissions->filter(function ($perm) {
                                    return str_starts_with($perm->name, 'menu.access.');
                                }),
                            ])->merge(
                                $permissions
                                    ->filter(function ($perm) {
                                        return !str_starts_with($perm->name, 'menu.access.');
                                    })
                                    ->groupBy(function ($perm) {
                                        return ucfirst(explode('.', $perm->name)[0]); // Customer, Hotspot, etc.
                                    }),
                            );
                        @endphp

                        <div class="form-group">
                            <label><strong>Assign Permissions</strong></label>

                            <div class="mb-3">
                                <input type="checkbox" id="checkAll">
                                <label for="checkAll">Check All</label>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-bordered table-striped table-hover">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th width="200">Permission Group</th>
                                            <th>Permissions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($grouped as $group => $perms)
                                            <tr>
                                                <td><strong>{{ $group }}</strong></td>
                                                <td>
                                                    @foreach ($perms as $permission)
                                                        <div class="form-check form-check-inline">
                                                            <input
                                                                class="form-check-input permission-checkbox group_{{ $group }}"
                                                                type="checkbox" name="permissions[]"
                                                                value="{{ $permission->name }}"
                                                                id="perm_{{ $permission->id }}">
                                                            <label class="form-check-label"
                                                                for="perm_{{ $permission->id }}">
                                                                {{ ucwords(str_replace($group . '.', '', $permission->name)) }}
                                                            </label>
                                                        </div>
                                                    @endforeach
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>


                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Create Role</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <div id="deleteModal" class="modal fade">
        <div class="modal-dialog modal-confirm">
            <form action="{{ route('admin.role.delete') }}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="modal-content">
                    <div class="modal-header flex-column">
                        <div class="icon-box">
                            <i class="fas fa-trash"></i>
                        </div>
                        <h4 class="modal-title w-100">Are you sure?</h4>
                        <input type="hidden" name="id" value="">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>Do you really want to delete these records? This process cannot be undone.</p>
                    </div>
                    <div class="modal-footer justify-content-center">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </div>
                </div>
            </form>
        </div>
    </div>


@endsection

@section('script')
    <script src="{{ asset('Backend/assets/js/__handle_submit.js') }}"></script>
    <script src="{{ asset('Backend/assets/js/delete_data.js') }}"></script>
    <script>
        document.getElementById('checkAll').addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.permission-checkbox');
            checkboxes.forEach(cb => cb.checked = this.checked);
        });
    </script>

    <script type="text/javascript">
        $(document).ready(function() {
            $("#role_datatable").DataTable();
            handleSubmit('#add_roll_form', '#addModal');
            handleSubmit('#edit_roll_form', '#editModal');
        });
        /** Handle Edit button click **/
        $('#role_datatable tbody').on('click', '.edit-btn', function() {
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
                        $('#popEditForm select[name="status"]').val(response.data.status).trigger(
                            'change');

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
        $('#role_datatable tbody').on('click', '.delete-btn', function() {
            var id = $(this).data('id');
            $('#deleteModal').modal('show');
            $("input[name*='id']").val(id);
        });

        $('#deleteModal form').submit(function(e) {
            e.preventDefault();
            var submitBtn = $(this).find('button[type="submit"]');
            var originalBtnText = submitBtn.html();
            submitBtn.html(
                '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>');
            var form = $(this);
            $.ajax({
                type: 'POST',
                url: form.attr('action'),
                data: form.serialize(),
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.message);
                        table.ajax.reload(null, false);
                        $('#deleteModal').modal('hide');
                    }
                },
                error: function(xhr) {
                    toastr.error(xhr.responseText);
                },
                complete: function() {
                    submitBtn.html(originalBtnText);
                }
            });
        });
    </script>
@endsection
