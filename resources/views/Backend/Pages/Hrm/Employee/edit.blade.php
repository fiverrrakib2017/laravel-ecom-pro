@extends('Backend.Layout.App')
@section('title','Add Employee | Admin Panel')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-9">
        <div class="card">
            <form action="{{ route('admin.hr.employee.update') }}" method="POST" enctype="multipart/form-data" id="addEmployeeForm">
                @csrf
                <div class="card-body">
                    <input type="hidden" name="id" value="{{ $data->id}}">
                    <!-- 1. Personal Information -->
                    <fieldset class="border p-4 shadow-sm rounded mb-4" style="border:2px #c9c9c9 dotted !important;">
                        <legend class="w-auto px-3 text-primary fw-bold">Personal Information</legend>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label>Full Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" placeholder="Enter Full Name" value="{{ $data->name?? '' }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Email <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control" placeholder="Enter Email" value="{{ $data->email ?? '' }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Phone <span class="text-danger">*</span></label>
                                <input type="text" name="phone" class="form-control" placeholder="Enter Phone" value="{{ $data->phone ?? '' }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Alternate Phone</label>
                                <input type="text" name="phone_2" class="form-control"  placeholder="Enter Phone" value="{{ $data->phone_2?? '' }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Hire Date <span class="text-danger">*</span></label>
                                <input type="date" name="hire_date" class="form-control" value="{{ $data->hire_date ?? '' }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Photo</label>
                                <input type="file" name="photo" class="form-control" id="photo" accept="image/*">

                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Address <span class="text-danger">*</span></label>
                                <input type="text" name="address" class="form-control"  placeholder="Enter Address" value="{{ $data->address ?? '' }}"  required>
                            </div>
                            <div class="col-md-6 mb-3">
                                @if(!empty($data->photo))
                                    <img id="preview" class="img-fluid" src="{{ asset('uploads/photos/' . $data->photo) }}" alt="Image Preview" style="max-width: 100px; max-height: 100px;" />
                                @else
                                    <img id="preview" class="img-fluid" src="#" alt="Image Preview" style="display: none; max-width: 100px; max-height: 100px;" />
                                @endif
                            </div>

                        </div>
                    </fieldset>

                    <!-- 2. Family & Personal Details -->
                    <fieldset class="border p-4 shadow-sm rounded mb-4" style="border:2px #c9c9c9 dotted !important;">
                        <legend class="w-auto px-3 text-primary fw-bold">Family & Personal Details</legend>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label>Father's Name <span class="text-danger">*</span></label>
                                <input type="text" name="father_name" class="form-control" placeholder="Enter Father's Name" value="{{ $data->father_name ?? '' }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Mother's Name <span class="text-danger">*</span></label>
                                <input type="text" name="mother_name" class="form-control" placeholder="Enter Mother's Name" value="{{ $data->mother_name ?? '' }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Gender <span class="text-danger">*</span></label>
                                <select name="gender" class="form-control">
                                    <option value="">Select Gender</option>
                                    <option value="male" @selected(isset($data) && $data->gender == 'male')>Male</option>
                                    <option value="female" @selected(isset($data) && $data->gender == 'female')>Female</option>
                                    <option value="other" @selected(isset($data) && $data->gender == 'other')>Other</option>
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label>Date of Birth <span class="text-danger">*</span></label>
                                <input type="date" name="birth_date" class="form-control" value="{{ $data->birth_date ?? '' }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>National ID <span class="text-danger">*</span></label>
                                <input type="text" name="national_id" class="form-control" placeholder="Enter National ID" value="{{ $data->national_id ?? '' }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Religion <span class="text-danger">*</span></label>
                                <input type="text" name="religion" class="form-control" placeholder="Enter Religion" value="{{ $data->religion ?? '' }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Blood Group </label>
                                <input type="text" name="blood_group" class="form-control"  placeholder="Enter Blood Group" value="{{ $data->blood_group ?? '' }}">
                            </div>
                        </div>
                    </fieldset>

                    <!-- 3. Educational & Job Information -->
                    <fieldset class="border p-4 shadow-sm rounded mb-4" style="border:2px #c9c9c9 dotted !important;">
                        <legend class="w-auto px-3 text-primary fw-bold">Educational & Job Information</legend>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label>Highest Education <span class="text-danger">*</span></label>
                                <input type="text" name="highest_education" class="form-control" placeholder="Enter Highest Education" value="{{ $data->highest_education ?? '' }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Previous School</label>
                                <input type="text" name="previous_school" class="form-control" placeholder="Enter Previous School" value="{{ $data->previous_school ?? '' }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Department <span class="text-danger">*</span></label>
                                <select name="department_id" class="form-control">
                                    <option value="">Select Department</option>
                                    @foreach($department as $dept)
                                        <option value="{{ $dept->id }}" @selected(isset($data) && $dept->id == $data->department_id)>
                                            {{ $dept->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label>Designation <span class="text-danger">*</span></label>
                                <select name="designation_id" class="form-control">
                                    <option value="">Select Designation</option>
                                    @foreach($designation as $designation)
                                        <option value="{{ $designation->id }}" @selected(isset($data) && $designation->id == $data->designation_id) >{{ $designation->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Salary <span class="text-danger">*</span></label>
                                <input type="number" name="salary" class="form-control" step="0.01" placeholder="Enter Salary" value="{{ intval($data->salary) ?? '' }}">
                            </div>
                        </div>
                    </fieldset>

                    <!-- 4. Emergency & Status -->
                    <fieldset class="border p-4 shadow-sm rounded mb-4" style="border:2px #c9c9c9 dotted !important;">
                        <legend class="w-auto px-3 text-primary fw-bold">Emergency Contact & Status</legend>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label>Emergency Contact Name <span class="text-danger">*</span></label>
                                <input type="text" name="emergency_contact_name" class="form-control" placeholder="Enter Emergency Contact Name" value="{{ $data->emergency_contact_name ?? '' }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Emergency Contact Phone <span class="text-danger">*</span></label>
                                <input type="text" name="emergency_contact_phone" class="form-control"  placeholder="Enter Emergency Contact Phone" value="{{ $data->emergency_contact_phone ?? '' }}">
                            </div>
                            <div class="col-md-6 mb-3">
                            <label>Status <span class="text-danger">*</span></label>
                            <select name="status" class="form-control">
                                <option value="active" @selected(isset($data) && $data->status == 'active')>Active</option>
                                <option value="inactive" @selected(isset($data) && $data->status == 'inactive')>Inactive</option>
                                <option value="resigned" @selected(isset($data) && $data->status == 'resigned')>Resigned</option>
                            </select>
                        </div>

                            <div class="col-md-6 mb-3">
                                <label>Remarks</label>
                                <textarea name="remarks" class="form-control" rows="3" placeholder="Enter Remarks" style="height:38px;">{{ $data->remarks ?? '' }}</textarea>
                            </div>
                        </div>
                    </fieldset>
                </div>

                <div class="card-footer text-end">
                    <button type="button" onclick="history.back();" class="btn btn-danger">Back</button>
                    <button type="submit" class="btn btn-success">Update Employee</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection



@section('script')
<script type="text/javascript">
    $(document).ready(function(){
        $(document).on('change', 'select[name="department_id"]', function () {
            /*Get all designations from Blade (passed from controller)*/
            const designations = @json($designation);

            /*Selected department ID*/
            const selectedDepartmentId = $(this).val();

            /*Filter designations based on department*/
            const filtered = designations.filter(item => item.department_id == selectedDepartmentId);

            /* Build dropdown options*/
            let options = '<option value="">--Select Section--</option>';
            filtered.forEach(item => {
                options += `<option value="${item.id}">${item.name}</option>`;
            });

            /* Update the designation dropdown*/
            const $designationDropdown = $('select[name="designation_id"]');
            $designationDropdown.html(options);
            $designationDropdown.select2(); // reinitialize select2
        });

        $('#photo').change(function() {
            let reader = new FileReader();
            reader.onload = function(e) {
                $('#preview').attr('src', e.target.result).show();
            }
            reader.readAsDataURL(this.files[0]);
        });

        $('#addEmployeeForm').submit(function(e) {
            e.preventDefault();

            /* Get the submit button */
            var submitBtn = $(this).find('button[type="submit"]');
            var originalBtnText = submitBtn.html();

            submitBtn.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span><span class="visually-hidden">Loading...</span>');
            submitBtn.prop('disabled', true);

            var form = $(this);
            var url = form.attr('action');
            /*Change to FormData to handle file uploads*/
            var formData = new FormData(this);

            /* Use Ajax to send the request */
            $.ajax({
                type: 'POST',
                url: url,
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                data: formData,
                contentType: false,
                processData: false,
                beforeSend: function() {
                    /* Disable the Form input */
                    form.find(':input').prop('disabled', true);
                    submitBtn.prop('disabled', true);
                },
                success: function(response) {

                    if (response.success) {
                        toastr.success(response.message);
                        submitBtn.html(originalBtnText);
                        submitBtn.prop('disabled', false);
                        setTimeout(() => {
                            location.reload();
                        }, 500);
                    }
                    if(response.success == false){
                        form.find(':input').prop('disabled', false);
                        toastr.error(response.message);
                        submitBtn.html(originalBtnText);
                        submitBtn.prop('disabled', false);
                    }
                },
                error: function(xhr, status, error) {
                    submitBtn.html(originalBtnText);
                    submitBtn.prop('disabled', false);
                    form.find(':input').prop('disabled', false);

                    if (xhr.status === 422) {
                        var errors = xhr.responseJSON.errors;
                        for (var field in errors) {
                            toastr.error(errors[field][0]);
                        }
                    } else {
                        toastr.error("Something went wrong! Please try again.");
                    }
                },
                complete: function() {
                    /* Reset button text and enable the button */
                    form.find(':input').prop('disabled', false);
                    submitBtn.html(originalBtnText);
                    submitBtn.prop('disabled', false);
                }
            });
        });


    });
  </script>


@endsection
