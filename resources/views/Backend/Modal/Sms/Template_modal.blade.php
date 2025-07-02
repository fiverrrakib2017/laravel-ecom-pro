<div class="modal fade bs-example-modal-lg" id="addSmsTemplateModal" tabindex="-1" role="dialog"
                     aria-labelledby="exampleModalLabel" aria-hidden="true">
                     <div class="modal-dialog " role="document">
                        <div class="modal-content col-md-12">
                           <div class="modal-header">
                              <h5 class="modal-title" id="ModalLabel"><span
                                 class="mdi mdi-account-check mdi-18px"></span> &nbsp;New Sms Template</h5>
                                 <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                  </button>
                           </div>
						   <div class="modal-body">
							  <form action="{{ route('admin.sms.template_Store') }}" id="SmsTemplateForm" method="POST" enctype="multipart/form-data">
                                @csrf
									<div class="form-group mb-2">
										<label>POP/Branch</label>
										<select name="pop_id" class="form-control" type="text" required>
                                            <option value="">---Select---</option>
                                            @php
                                                $pops = \App\Models\Pop_branch::where('status','1')->latest()->get();
                                            @endphp
                                            @foreach ($pops as $item)
                                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                                            @endforeach
                                        </select>
									</div>
									<div class="form-group mb-2">
										<label>Template Name</label>
										<input name="name" placeholder="Enter Template Name" class="form-control" type="text" required>
									</div>

									<div class="form-group mb-2">
										<label>SMS </label>
										<textarea name="message" placeholder="Enter SMS" class="form-control" type="text"></textarea>
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
