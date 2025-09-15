<form class="row g-3 align-items-end" id="search_box">
    <div class="col-md-3">
        <div class="form-group">
            <label for="pop_id" class="form-label">POP/Branch Name <span class="text-danger">*</span></label>
            <select name="pop_id" id="pop_id" class="form-control" required>
                <option value="">Select POP Branch</option>
                @php
                    $branch_user_id = Auth::guard('admin')->user()->pop_id ?? null;
                    if (empty($pop_id)) {
                        $pop_id = $branch_user_id;
                    }
                    if ($branch_user_id != null) {
                        $pops = App\Models\Pop_branch::where('status', '1')->where('id', $branch_user_id)->get();
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
    </div>

    <div class="col-md-3">
        <div class="form-group">
            <label for="area" class="form-label">Area <span class="text-danger">*</span></label>
            <select name="area_id" id="area_id" class="form-control" required>
                <option value="">Select Area</option>
                @php
                    $branch_user_id = Auth::guard('admin')->user()->pop_id ?? null;
                    if ($branch_user_id != null && $branch_user_id != 0) {
                        $areas = App\Models\Pop_area::where('pop_id', $branch_user_id)->latest()->get();
                    } else {
                        $areas = \App\Models\Pop_area::latest()->get();
                    }
                @endphp
                @foreach ($areas as $item)
                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="col-md-3">
        <div class="form-group">
            <label for="customer_status" class="form-label">Status </label>
            <select name="customer_status" id="customer_status" class="form-select" style="width: 100%;" required>
                <option value="">---Select---</option>
                <option value="active">Active</option>
                <option value="online">Online</option>
                <option value="offline">Offline</option>
                <option value="blocked">Blocked</option>
                <option value="expired">Expired</option>
                <option value="disabled">Disabled</option>
            </select>
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label for="customer_expire_date" class="form-label">Expire Date</label>
            <input type="date" name="customer_expire_date" id="customer_expire_date" class="form-control">
        </div>
    </div>

    <div class="col-md-3 d-grid">
        <div class="form-group">
            <button type="button" name="search_btn" class="btn btn-success">
                <i class="fas fa-search me-1"></i> Search Now
            </button>
        </div>
    </div>
</form>

<script type="text/javascript">
    $(document).ready(function() {
        /** Handle pop branch button click **/
        $(document).on('change', 'select[name="pop_id"]', function() {
            var pop_id = $(this).val();
            if (pop_id) {
                var $area_url = "{{ route('admin.pop.area.get_pop_wise_area', ':id') }}".replace(':id',
                    pop_id);
                var $package_url = "{{ route('admin.pop.branch.get_pop_wise_package', ':id') }}"
                    .replace(':id',
                        pop_id);
                load_dropdown($area_url, 'select[name="area_id"]');
            } else {
                $(' select[name="area_id"]').html('<option value="">Select Area</option>');
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
