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
