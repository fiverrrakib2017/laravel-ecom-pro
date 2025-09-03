 @php

$app_settings= \App\Models\Website_information::first();

@endphp

 <div class="card">
     <div class="card-header">
         <h3 class="card-title">Support & Quick Actions</h3>
     </div>
     <div class="card-body">
         <a href="#" class="btn btn-success btn-block mb-2"><i class="fas fa-money-check-alt mr-1"></i> Pay Now</a>
         <a href="https://speed.cloudflare.com" target="_blank" class="btn btn-info btn-block mb-2"><i
                 class="fas fa-tachometer-alt mr-1"></i> Speed
             Test</a>
            <button type="button" class="btn btn-warning btn-block mb-3" data-toggle="modal" data-target="#supportModal"><i
                 class="fas fa-headset mr-1"></i> Create Support Ticket</button>
         <div>
             <p class="mb-1"><i class="fas fa-phone-alt mr-2"></i>{{$app_settings->phone_number ?? ''}} (24/7)</p>
             <p class="mb-0"><i class="far fa-envelope mr-2"></i>{{$app_settings->email ?? ''}} </p>
         </div>
     </div>
 </div>
