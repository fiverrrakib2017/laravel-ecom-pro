<div class="modal fade bs-example-modal-lg" id="addModal" tabindex="-1" role="dialog"
                     aria-labelledby="exampleModalLabel" aria-hidden="true">
                     <div class="modal-dialog " role="document">
                        <div class="modal-content col-md-12">
                           <div class="modal-header">
                              <h5 class="modal-title" id="ModalLabel"><span
                                 class="mdi mdi-account-check mdi-18px"></span> &nbsp;New POP/Area</h5>
                                 <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                  </button>
                           </div>
						   <div class="modal-body">
							  <form action="{{ route('admin.pop.area.store') }}" id="popForm" method="POST" enctype="multipart/form-data">
                                @csrf
									<div class="form-group mb-2">
										<label>POP/Branch</label>
										<select name="pop_id" class="form-control" type="text" required>
                                            <option value="">---Select---</option>
                                            @php
                                            $branch_user_id = Auth::guard('admin')->user()->pop_id ?? null;
                                                if($branch_user_id != null){
                                                    $pops = \App\Models\Pop_branch::where('status','1')->where('id', $branch_user_id)->get();
                                                }else{
                                                    $pops = \App\Models\Pop_branch::where('status','1')->latest()->get();
                                                }
                                            @endphp
                                            @foreach ($pops as $item)
                                                <option value="{{ $item->id }}" {{ $branch_user_id == $item->id ? 'selected' : '' }}>{{ $item->name }}</option>
                                            @endforeach
                                        </select>
									</div>
									<div class="form-group mb-2">
										<label>Area Name</label>
										<input name="name" placeholder="Enter Fullname" class="form-control" type="text" required>
									</div>

									<div class="form-group mb-2">
										<label>Billing Cycle</label>
										<select name="billing_cycle" placeholder="Enter " class="form-control" type="text">
                                            @php
                                                for($i=1; $i<=30; $i++){
                                                    echo "<option value='$i'>$i</option>";
                                                }
                                            @endphp
                                        </select>
									</div>
									<div class="modal-footer ">
										<button data-dismiss="modal" type="button" class="btn btn-danger">Cancel</button>
										<button type="submit" class="btn btn-success">Save Changes</button>
									</div>
								</form>
							</div>
                        </div>
                     </div>
                  </div>
