@extends('Backend.Layout.App')
@section('title', ' User List | Admin Panel')
@section('style')
@endsection
@section('content')
    <div class="row">
        <div class="col-md-12 ">
            <div class="card">
                <div class="card-body">
                    <button data-toggle="modal" data-target="#addModal" type="button" class=" btn btn-success mb-2"><i class="mdi mdi-account-plus"></i> Add New User</button>

                    <div class="table-responsive" id="tableStyle">
                        <table id="user_datatable" class="table table-bordered dt-responsive nowrap"
                            style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Type</th>
                                    <th>Full Name</th>
                                    <th>Username</th>
                                    <th>Email</th>
                                    <th>Mobile</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($data as $item)
                                    <tr>
                                        <td>{{$item->id}}</td>
                                        <td>
                                            @if ($item->user_type == 1)
                                                <span class="badge badge-success">Main User</span>
                                            @elseif ($item->user_type == 2)
                                                <span class="badge badge-primary">Branch User</span>
                                            @else
                                                <span class="badge badge-secondary">Unknown</span>
                                            @endif

                                        </td>
                                        <td>{{$item->name}}</td>
                                        <td>{{$item->username}}</td>
                                        <td>{{$item->email}}</td>
                                        <td>{{$item->phone}}</td>
                                        <td>
                                            <button class="btn-sm btn btn-success edit-btn" data-id="{{$item->id}}"><i class="fas fa-edit"></i></button>
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
    <!----------------Add Modal----------------->
    <div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="ModalLabel">  <i class="fas fa-user-check"></i> &nbsp; Create User</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <!-- Left Column -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><strong>Name</strong></label>
                                    <input type="text" name="name" class="form-control" placeholder="Enter Name" required>
                                </div>

                                <div class="form-group">
                                    <label><strong>Username</strong></label>
                                    <input type="text" name="username" class="form-control" placeholder="Enter Username" required>
                                </div>

                                <div class="form-group">
                                    <label><strong>Email address</strong></label>
                                    <input type="email" name="email" class="form-control" placeholder="Enter Email" required>
                                </div>
                            </div>

                            <!-- Right Column -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><strong>Password</strong></label>
                                    <input type="password" name="password" class="form-control" placeholder="Enter Password" required>
                                </div>

                                <div class="form-group">
                                    <label><strong>Confirm Password</strong></label>
                                    <input type="password" name="password_confirmation" class="form-control" placeholder="Confirm Password" required>
                                </div>

                                <div class="form-group">
                                    <label><strong>Select Role</strong></label>
                                    <select name="role" class="form-control" required>
                                        <option value="">Select Role</option>
                                        @foreach($roles as $role)
                                            <option value="{{ $role->name }}">{{ ucfirst($role->name) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button"  data-dismiss="modal" class="btn btn-danger">Close</button>
                        <button type="submit" class="btn btn-success"><i class="fas fa-user-check"></i>&nbsp; Create User</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!----------------Edit Modal----------------->
    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="ModalLabel">  <i class="fas fa-user-check"></i> &nbsp; Update User</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="" method="POST">
                    @csrf
                    <input type="text" name="id" class="d-none">
                    <div class="modal-body">
                        <div class="row">
                            <!-- Left Column -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><strong>Name</strong></label>
                                    <input type="text" name="name" class="form-control" placeholder="Enter Name" required>
                                </div>

                                <div class="form-group">
                                    <label><strong>Username</strong></label>
                                    <input type="text" name="username" class="form-control" placeholder="Enter Username" required>
                                </div>

                                <div class="form-group">
                                    <label><strong>Email address</strong></label>
                                    <input type="email" name="email" class="form-control" placeholder="Enter Email" required>
                                </div>
                            </div>

                            <!-- Right Column -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><strong>Password</strong></label>
                                    <input type="password" name="password" class="form-control" placeholder="Enter Password" >
                                </div>

                                <div class="form-group">
                                    <label><strong>Confirm Password</strong></label>
                                    <input type="password" name="password_confirmation" class="form-control" placeholder="Confirm Password" >
                                </div>

                                <div class="form-group">
                                    <label><strong>Select Role</strong></label>
                                    <select name="role" id="role" class="form-control">

                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button"  data-dismiss="modal" class="btn btn-danger">Close</button>
                        <button type="submit" class="btn btn-success"><i class="fas fa-user-check"></i>&nbsp; Update User</button>
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
            $("#user_datatable").Datatable();
            handleSubmit('#popForm', '#addModal');
            handleSubmit('#popEditForm', '#editPopBranchModal');


        });
        /** Handle Edit button click **/
        $('#user_datatable tbody').on('click', '.edit-btn', function() {
            var id = $(this).data('id');
            $.ajax({
                url: "{{ route('admin.user.get_user', ':id') }}".replace(':id', id),
                method: 'GET',
                success: function(response) {
                    if (response) {


                        var admin = response.admin;
                        var roles = response.roles;

                        $('input[name="id"]').val(admin.id);
                        $('input[name="name"]').val(admin.name);
                        $('input[name="username"]').val(admin.username);
                        $('input[name="email"]').val(admin.email);

                        var roleSelect = $('select[name="role"]');
                        roleSelect.empty();
                        $.each(roles, function (i, role) {
                            var selected = (role.name === response.current_role) ? 'selected' : '';
                            roleSelect.append('<option value="' + role.name + '" ' + selected + '>' + role.name + '</option>');
                        });

                        $('#editModal').modal('show');
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
        $('#user_datatable tbody').on('click', '.delete-btn', function() {
            var id = $(this).data('id');
            var deleteUrl = "{{ route('admin.pop.delete', ':id') }}".replace(':id', id);

            $('#deleteForm').attr('action', deleteUrl);
            $('#deleteModal').find('input[name="id"]').val(id);
            $('#deleteModal').modal('show');
        });
    </script>
@endsection
