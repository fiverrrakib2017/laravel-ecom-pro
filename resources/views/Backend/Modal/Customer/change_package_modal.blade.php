<!-- Modal for Change Customer Expire Date -->
<div class="modal fade " id="bulk_change_packageModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog " role="document">
        <div class="modal-content col-md-12">
            <div class="modal-header">
                <h5 class="modal-title" id="ModalLabel"> <i class="fas fa-calendar-alt"></i>
                    &nbsp;Change Customer Package </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="alert alert-success" id="selectedCustomerCount"></div>
                <form action="{{route('admin.customer.bulk.package.update')}}" id="bulk_change_packageForm" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="pop_id" class="form-label">POP/Branch Name <span
                                class="text-danger">*</span></label>
                        <select name="pop_id" id="pop_id" class="form-control" required>
                            <option value="">Select POP Branch</option>
                            @php
                                $branch_user_id = Auth::guard('admin')->user()->pop_id ?? null;
                                if (empty($pop_id)) {
                                    $pop_id = $branch_user_id;
                                }
                                if ($branch_user_id != null) {
                                    $pops = App\Models\Pop_branch::where('status', '1')
                                        ->where('id', $branch_user_id)
                                        ->get();
                                } else {
                                    $pops = App\Models\Pop_branch::where('status', '1')->latest()->get();
                                }
                            @endphp
                            @foreach ($pops as $item)
                                <option value="{{ $item->id }}" @if ($item->id == $pop_id) selected @endif>
                                    {{ $item->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="area" class="form-label">Area <span class="text-danger">*</span></label>
                        <select name="area_id" id="area_id" class="form-control" required>
                            <option value="">Select Area</option>
                        </select>
                    </div>
                    <div class="form-group mb-2">
                        <label for="customer_expire_date" class="form-label">Package Name</label><span
                            class="text-danger">*</span>
                        <select type="test" name="customer_package_id" id="customer_package_id" class="form-select"
                            required></select>
                    </div>
                    <div class="modal-footer ">
                        <button data-dismiss="modal" type="button" class="btn btn-danger">Close</button>
                        <button type="submit" class="btn btn-success">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<script type="text/javascript">
    $(document).ready(function() {
        /** Handle pop branch button click **/
        $(document).on('change', '#bulk_change_packageModal select[name="pop_id"]', function() {
            var pop_id = $(this).val();
            if (pop_id) {
                var $area_url = "{{ route('admin.pop.area.get_pop_wise_area', ':id') }}".replace(':id',
                    pop_id);
                var $package_url = "{{ route('admin.pop.branch.get_pop_wise_package', ':id') }}"
                    .replace(':id',
                        pop_id);
                load_dropdown($area_url, '#bulk_change_packageModal select[name="area_id"]');
                load_dropdown($package_url, '#bulk_change_packageModal select[name="customer_package_id"]');
            } else {
                $('#bulk_change_packageModal select[name="area_id"]').html(
                    '<option value="">Select Area</option>');
                $('#bulk_change_packageModal select[name="customer_package_id"]').html(
                    '<option value="">---Select---</option>');
            }

        });

        function load_dropdown(url, target_url) {
            $.ajax({
                url: url,
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    $(target_url).empty().append('<option value="">---Select---</option>');
                    $.each(data.data, function(key, value) {
                        $(target_url).append('<option value="' + value.id + '">' + value
                            .name +
                            '</option>');
                    });
                }
            });
        }
    });
</script>
