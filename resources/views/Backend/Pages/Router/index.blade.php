@extends('Backend.Layout.App')
@section('title', 'Dashboard | Admin Panel')
@section('style')
@endsection
@section('content')
    <div class="row">
        <div class="col-md-12 ">
            <div class="card">
                <div class="card-body">
                    <button data-toggle="modal" data-target="#addModal" type="button" class=" btn btn-success mb-2"><i
                            class="mdi mdi-account-plus"></i>
                        Add New Router</button>

                    <div class="table-responsive" id="tableStyle">
                        <table id="datatable1" class="table table-hover text-nowrap table-bordered table-striped">
                            <thead class="">
                                <tr>
                                    <th>ID</th>
                                    <th>Details</th>
                                    <th>Online User</th>
                                    <th>Location</th>
                                    <th>IP</th>
                                    <th>Secret</th>
                                    <th>Api User</th>
                                    <th>Description</th>
                                    <th>Server</th>
                                    {{-- <th>Action</th> --}}
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($routers as $item)
                                    @php
                                        $router_data = $mikrotik_data->firstWhere('router_id', $item->id);
                                        $uptime = $router_data['uptime'] ?? 'N/A';
                                        preg_match('/(?:(\d+)w)?(?:(\d+)d)?(?:(\d+)h)?(?:(\d+)m)?(?:(\d+)s)?/', $uptime, $matches);
                                        $weeks = $matches[1] ?? 0;
                                        $days = $matches[2] ?? 0;
                                        $hours = $matches[3] ?? 0;
                                        $minutes = $matches[4] ?? 0;
                                        $seconds = $matches[5] ?? 0;
                                    @endphp
                                    <tr>
                                        <td>{{ $item->id }}</td>
                                        <td>
                                            <strong>{{ $item->name }}</strong><br>
                                            <small>Port: {{ $item->port }}</small><br>
                                            <small>Uptime:
                                                @if ($uptime !== 'N/A')
                                                    <span class="badge bg-primary">{{ $weeks }}w</span>
                                                    <span class="badge bg-success">{{ $days }}d</span>
                                                    <span class="badge bg-warning text-dark">{{ $hours }}h</span>
                                                    <span class="badge bg-info text-dark">{{ $minutes }}m</span>
                                                    <span class="badge bg-secondary">{{ $seconds }}s</span>
                                                @else
                                                    <span class="badge bg-danger">N/A</span>
                                                @endif
                                            </small><br>
                                            <small>Version: {{ $router_data['version'] ?? 'N/A' }}</small><br>
                                            <small>Hardware: {{ $router_data['hardware'] ?? 'N/A' }}</small><br>
                                            <small>CPU: {{ $router_data['cpu'] ?? 'N/A' }}</small>
                                        </td>
                                        <td>
                                            @if (isset($router_data['online_users']))
                                                <a target="__blank"
                                                    href="{{ route('admin.router.ppp.users.index', $router_data['router_id']) }}">
                                                    <span class="badge bg-success">{{ $router_data['online_users'] }} Online</span>
                                                </a><br>
                                                <span class="badge bg-danger">{{ $router_data['offline_users'] }} Offline</span>
                                            @elseif(isset($router_data['error']))
                                                <span class="badge bg-danger">Error: {{ $router_data['error'] }}</span>
                                            @else
                                                <span class="badge bg-danger">No Data</span>
                                            @endif
                                        </td>
                                        <td>{{ $item->location }}</td>
                                        <td>{{ $item->ip_address }}</td>
                                        <td>{{ $item->password }}</td>
                                        <td>{{ $item->username }}</td>
                                        <td>{{ $item->remarks }}</td>
                                        <td>
                                            <span class="badge bg-{{ $item->status === 'active' ? 'success' : 'secondary' }}">
                                                {{ ucfirst($item->status) }}
                                            </span>
                                        </td>
                                         <td>
                                            <button class="btn btn-sm btn-primary edit-btn mb-1" data-id="{{ $item->id }}">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                           {{-- <button class="btn btn-sm btn-danger delete-btn" data-id="{{ $item->id }}">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td> --}}
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <!-- Add Modal -->
    <!-- Add Router Modal -->
    <div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addRouterModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addRouterModalLabel">Add New Router</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('admin.router.store') }}" method="POST" id="routerForm">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="name">Router Name</label>
                                    <input type="text" class="form-control" id="name" name="name"
                                        placeholder="Enter Router Name" required>
                                </div>
                                <div class="form-group mb-3">
                                    <label for="">POP/Branch</label>
                                    <select type="text" class="form-control" id="pop_id" name="pop_id" required>
                                        <option value="">---Select---</option>
                                        @php
                                            $pop = App\Models\Pop_branch::latest()->get();
                                        @endphp
                                        @foreach ($pop as $item)
                                            <option value="{{ $item->id }}">{{ $item->name }} </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group mb-3">
                                    <label for="ip_address">IP Address</label>
                                    <input type="text" class="form-control" id="ip_address" name="ip_address"
                                        placeholder="Enter IP Address" required>
                                </div>
                                <div class="form-group mb-3">
                                    <label for="username">Username</label>
                                    <input type="text" class="form-control" id="username" name="username"
                                        placeholder="Enter Username" required>
                                </div>
                                <div class="form-group mb-3">
                                    <label for="password">Password</label>
                                    <input type="password" class="form-control" id="password" name="password"
                                        placeholder="Enter Password" required>
                                </div>

                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="status">Status</label>
                                    <select class="form-control" id="status" name="status" required>
                                        <option value="active">Active</option>
                                        <option value="inactive">Inactive</option>
                                    </select>
                                </div>
                                <div class="form-group mb-3">
                                    <label for="api_version">API Version</label>
                                    <input type="text" class="form-control" id="api_version" name="api_version"
                                        placeholder="e.g., 6.48">
                                </div>
                                <div class="form-group mb-3">
                                    <label for="location">Location</label>
                                    <input type="text" class="form-control" id="location" name="location"
                                        placeholder="Enter Location">
                                </div>
                                <div class="form-group mb-3">
                                    <label for="remarks">Remarks</label>
                                    <textarea class="form-control" id="remarks" style="height: 38px;" name="remarks" rows="3" placeholder="Any additional remarks"></textarea>
                                </div>
                                <div class="form-group mb-3">
                                    <label for="port">API Port</label>
                                    <input type="text" class="form-control" id="port" name="port" value="8728"
                                        placeholder="Enter Port" required>
                                </div>
                            </div>
                        </div>
                        <!-- Checkbox Use Radius -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group clearfix">
                                    <div class="icheck-primary d-inline">
                                        <input type="checkbox" id="sendMessageCheckbox" name="radius_server" value="1">
                                        <label for="sendMessageCheckbox">
                                            Use Radius Server
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-success">Save Router</button>
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
            handleSubmit('#routerForm', '#addModal');


        });

        /** Handle Edit button click **/
        $('#datatable1 tbody').on('click', '.edit-btn', function() {
            var id = $(this).data('id');
            $.ajax({
                url: "{{ route('admin.router.edit', ':id') }}".replace(':id', id),
                method: 'GET',
                success: function(response) {
                    console.log(response);
                    if (response.success) {
                        $('#routerForm').attr('action', "{{ route('admin.router.update', ':id') }}"
                            .replace(':id', id));
                        $('#ModalLabel').html(
                            '<span class="mdi mdi-account-edit mdi-18px"></span> &nbsp;Edit Router');
                        $('#routerForm input[name="name"]').val(response.data.name);
                        $('#routerForm input[name="name"]').val(response.data.name);
                        $('#routerForm select[name="pop_id"]').val(response.data.pop_id).trigger('change');
                        $('#routerForm input[name="ip_address"]').val(response.data.ip_address);
                        $('#routerForm input[name="api_version"]').val(response.data.api_version);
                        $('#routerForm input[name="username"]').val(response.data.username);
                        $('#routerForm input[name="password"]').val(response.data.password);
                        $('#routerForm input[name="port"]').val(response.data.port);
                        $('#routerForm select[name="status"]').val(response.data.status);
                        $('#routerForm input[name="location"]').val(response.data.location);
                        $('#routerForm textarea[name="remarks"]').val(response.data.remarks);
                        // Show the modal
                        $('#addModal').modal('show');
                    } else {
                        toastr.error('Failed to fetch data.');
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
            var deleteUrl = "{{ route('admin.router.delete', ':id') }}".replace(':id', id);

            $('#deleteForm').attr('action', deleteUrl);
            $('#deleteModal').find('input[name="id"]').val(id);
            $('#deleteModal').modal('show');
        });
    </script>
@endsection
