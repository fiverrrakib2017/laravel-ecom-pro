@extends('Backend.Layout.App')
@section('title','Dashboard | Admin Panel')

@section('content')
<div class="row">
    <div class="col-md-12 ">
        <div class="card">
        <div class="card-header">
          <button data-toggle="modal" data-target="#addModal"  class="btn btn-success "><i class="mdi mdi-account-plus"></i>
          Add New Account</button>
          </div>
            <div class="card-body">
                <div class="table-responsive" id="tableStyle">
                    <table id="datatable1" class="table table-striped table-bordered    " cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th>ID</th>
                                 <th>Parent Account</th>
                                <th>Name</th>
                                <th>Type</th>
                                <th>Description</th>

                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>
{{-- Add Modal --}}
<div class="modal fade" id="addModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <form action="{{route('admin.account.store')}}" method="POST" enctype="multipart/form-data" id="accountForm">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Account</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">

                    <div class="form-group">
                        <label for="name">Account Name</label>
                        <input type="text" name="name" id="name" class="form-control" placeholder="Enter Account Name" required>
                    </div>

                    <div class="form-group">
                        <label for="type">Account Type</label>
                        <select name="type" id="type" class="form-control" required>
                            <option value="">---Select---</option>
                            <option value="Income">Income</option>
                            <option value="Expense">Expense</option>
                            <option value="Asset">Asset</option>
                            <option value="Liability">Liability</option>
                            <option value="Equity">Equity</option>
                        </select>
                    </div>

                   <div class="form-group">
                        <label for="parent_account_id">Parent Account</label>
                        <select name="parent_account_id" id="parent_account_id" class="form-control">
                            <option value="">-- Select Parent Account (Optional) --</option>
                            @foreach($accounts as $acc)
                                <option value="{{ $acc->id }}">{{ $acc->name }} ({{ $acc->type }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea name="description" id="description" placeholder="Enter Your Description" class="form-control"></textarea>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Save Changes</button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- edit Modal --}}
<div class="modal fade" id="editModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <form action="{{route('admin.account.update')}}" method="POST" enctype="multipart/form-data" id="accountForm">
            @csrf
            <input type="hidden" name="id">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Update Account</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">

                    <div class="form-group">
                        <label for="name">Account Name</label>
                        <input type="text" name="name" id="name" class="form-control" placeholder="Enter Account Name" required>
                    </div>

                    <div class="form-group">
                        <label for="type">Account Type</label>
                        <select name="type" id="type" class="form-control" required>
                            <option value="">---Select---</option>
                            <option value="Income">Income</option>
                            <option value="Expense">Expense</option>
                            <option value="Asset">Asset</option>
                            <option value="Liability">Liability</option>
                            <option value="Equity">Equity</option>
                        </select>
                    </div>

                   <div class="form-group">
                        <label for="parent_account_id">Parent Account</label>
                        <select name="parent_account_id" id="parent_account_id" class="form-control">
                            <option value="">-- Select Parent Account (Optional) --</option>
                            @foreach($accounts as $acc)
                                <option value="{{ $acc->id }}">{{ $acc->name }} ({{ $acc->type }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea name="description" id="description" placeholder="Enter Your Description" class="form-control"></textarea>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Save Changes</button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div id="deleteModal" class="modal fade">
    <div class="modal-dialog modal-confirm">
        <form action="{{route('admin.account.delete')}}" method="post" enctype="multipart/form-data">
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
<script type="text/javascript">
  $(document).ready(function(){

    /* Initialize DataTable */
    var table = $("#datatable1").DataTable({
      "processing":true,
      "responsive": true,
      "serverSide":true,
      ajax: {
            url: "{{ route('admin.account.all_data') }}",
            type: 'GET',
            data: function(d) {
              d.class_id = $('#search_class_id').val();
            },
            beforeSend: function(request) {
                request.setRequestHeader("X-CSRF-TOKEN", $('meta[name="csrf-token"]').attr('content'));
            }
        },
      language: {
        searchPlaceholder: 'Search...',
        sSearch: '',
        lengthMenu: '_MENU_ items/page',
      },
        "columns": [
                { data: 'id' },
                { data: 'parent_name',
                    render: function(data) {
                        return data ?? 'N/A';
                    }
                },
                { data: 'name' },
                { data: 'type' },
                { data: 'description' },

                { data: null, render: function(data,type,row) {
                    return `
                        <button type="button" class="btn btn-primary btn-sm" name="edit_button" data-id="${row.id}"><i class="fa fa-edit"></i></button>
                        <button class="btn btn-danger btn-sm delete-btn" data-toggle="modal" data-target="#deleteModal" data-id="${row.id}"><i class="fa fa-trash"></i></button>
                        `;
                }}
            ],
      order:[ [0, "desc"] ],
    });



    /* General form submission handler*/
    function handleFormSubmit(modalId, form) {
        $(modalId + ' form').submit(function(e){
            e.preventDefault();
            var submitBtn = $(this).find('button[type="submit"]');
            var originalBtnText = submitBtn.html();
            submitBtn.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>');
            submitBtn.prop('disabled', true);

            var formData = new FormData(this);
            $.ajax({
                type: $(this).attr('method'),
                url: $(this).attr('action'),
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {
                    if (response.success) {
                        toastr.success(response.message);
                        table.ajax.reload(null, false);
                        $(modalId).modal('hide');
                        form[0].reset();
                    }
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        var errors = xhr.responseJSON.errors;
                        $.each(errors, function(field, messages) {
                            $.each(messages, function(index, message) {
                                toastr.error(message);
                            });
                        });
                    } else {
                        toastr.error('An error occurred. Please try again.');
                    }
                },
                complete: function() {
                    submitBtn.html(originalBtnText);
                    submitBtn.prop('disabled', false);
                }
            });
        });
    }

    /* Handle Add and Edit Form */
    handleFormSubmit("#addModal", $('#addModal form'));
    handleFormSubmit("#editModal", $('#editModal form'));

    /* Edit button click handler*/
    $(document).on("click", "button[name='edit_button']", function() {
        var _id = $(this).data("id");
        var editUrl = '{{ route("admin.account.edit", ":id") }}';
        var url = editUrl.replace(':id', _id);
        $.ajax({
          url: url,
          type: "GET",
          dataType: 'json',
          success: function(response) {
            if (response.success) {
                $('#editModal').modal('show');
                $('#editModal input[name="id"]').val(response.data.id);
                $('#editModal input[name="name"]').val(response.data.name);
                $('#editModal select[name="type"]').val(response.data.type).trigger('change');
                $('#editModal input[name="description"]').val(response.data.description);
                $('#editModal select[name="parent_account_id"]').val(response.data.parent_account_id).trigger('change');
            }
          },
          error: function(xhr) {
              toastr.error('Failed to fetch ');
          }
        });
    });

    /* Handle Delete button click and form submission*/
    $('#datatable1 tbody').on('click', '.delete-btn', function () {
        var id = $(this).data('id');
        $('#deleteModal').modal('show');
        $("input[name*='id']").val(id);
    });

    $('#deleteModal form').submit(function(e){
        e.preventDefault();
        var submitBtn = $(this).find('button[type="submit"]');
        var originalBtnText = submitBtn.html();
        submitBtn.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>');
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
});

  </script>


@endsection
