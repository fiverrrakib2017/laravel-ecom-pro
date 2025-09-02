<style>
  #faqCard .faq-item a { text-decoration: none; }
  #faqCard .faq-item .rotate { transition: transform .2s ease; }
  #faqCard .collapse.show + .rotate,
  #faqCard .faq-item a[aria-expanded="true"] .rotate { transform: rotate(180deg); }
</style>

<!-- FAQs (Pro) -->
<div class="card" id="faqCard">
  <div class="card-header d-flex align-items-center justify-content-between">
    <h3 class="card-title mb-0">
      <i class="far fa-question-circle mr-2"></i> FAQs
    </h3>

    <!-- Search + Controls -->
    <div class="card-tools" style="width: 340px;">
      <div class="input-group input-group-sm">
        <input id="faqSearch" type="text" class="form-control" placeholder="Search FAQ... (বাংলা/English)">
        <div class="input-group-append">
          <button id="faqExpandAll" class="btn btn-default" data-toggle="tooltip" title="Expand all">
            <i class="fas fa-plus"></i>
          </button>
          <button id="faqCollapseAll" class="btn btn-default" data-toggle="tooltip" title="Collapse all">
            <i class="fas fa-minus"></i>
          </button>
        </div>
      </div>
    </div>
  </div>

  <div class="card-body p-0">
    <div id="faqAccordion" class="accordion">
      <!-- Item 1 -->
      <div class="faq-item border-bottom">
        <a class="d-flex justify-content-between align-items-center py-3 px-3 text-body" data-toggle="collapse" href="#faq1" aria-expanded="true" aria-controls="faq1">
          <span>কীভাবে বিল পেমেন্ট করবো?</span>
          <i class="fas fa-chevron-down small rotate"></i>
        </a>
        <div id="faq1" class="collapse show" data-parent="#faqAccordion">
          <div class="px-3 pb-3 text-muted">
            bKash/Nagad থেকে “Pay Now” চাপুন; রেফারেন্সে আপনার Username দিন। পেমেন্ট কনফার্ম হলে কানেকশন তাৎক্ষণিক অ্যাক্টিভেট হবে।
          </div>
        </div>
      </div>

      <!-- Item 2 -->
      <div class="faq-item border-bottom">
        <a class="d-flex justify-content-between align-items-center py-3 px-3 text-body" data-toggle="collapse" href="#faq2" aria-expanded="false" aria-controls="faq2">
          <span>নেট না চললে কী করবো?</span>
          <i class="fas fa-chevron-down small rotate"></i>
        </a>
        <div id="faq2" class="collapse" data-parent="#faqAccordion">
          <div class="px-3 pb-3 text-muted">
            1) ONU/Router ৩০ সেকেন্ড বন্ধ করে চালু করুন। 2) LAN/Wi-Fi রিকানেক্ট করুন। 3) সমস্যা থাকলে Support টিকিট দিন।
          </div>
        </div>
      </div>

      <!-- Item 3 -->
      <div class="faq-item border-bottom">
        <a class="d-flex justify-content-between align-items-center py-3 px-3 text-body" data-toggle="collapse" href="#faq3" aria-expanded="false" aria-controls="faq3">
          <span>স্ট্যাটিক IP দরকার?</span>
          <i class="fas fa-chevron-down small rotate"></i>
        </a>
        <div id="faq3" class="collapse" data-parent="#faqAccordion">
          <div class="px-3 pb-3 text-muted">
            স্ট্যাটিক IP আলাদা চার্জে দেওয়া হয়। অনুগ্রহ করে সাপোর্ট/সেলস টিমের সাথে যোগাযোগ করুন।
          </div>
        </div>
      </div>

      <!-- (ঐচ্ছিক) আরও কিছু হাই-ভ্যালু প্রশ্ন -->
      <div class="faq-item border-bottom">
        <a class="d-flex justify-content-between align-items-center py-3 px-3 text-body" data-toggle="collapse" href="#faq4">
          <span>PPPoE পাসওয়ার্ড কিভাবে রিসেট করবো?</span>
          <i class="fas fa-chevron-down small rotate"></i>
        </a>
        <div id="faq4" class="collapse" data-parent="#faqAccordion">
          <div class="px-3 pb-3 text-muted">
            পোর্টাল থেকে “Change Password” রিকোয়েস্ট করুন অথবা সাপোর্টে জানিয়ে রিসেট করিয়ে নিন।
          </div>
        </div>
      </div>

      <div class="faq-item border-bottom">
        <a class="d-flex justify-content-between align-items-center py-3 px-3 text-body" data-toggle="collapse" href="#faq5">
          <span>প্যাকেজ আপগ্রেড করলে কত সময় লাগে?</span>
          <i class="fas fa-chevron-down small rotate"></i>
        </a>
        <div id="faq5" class="collapse" data-parent="#faqAccordion">
          <div class="px-3 pb-3 text-muted">
            সাধারণত ১৫–৩০ মিনিটের মধ্যে কার্যকর হয় (অফিস সময়ে)। দ্রুত করতে সাপোর্টে জানান।
          </div>
        </div>
      </div>

      <!-- No result -->
      <div id="faqNoResult" class="p-4 text-center text-muted d-none">
        <i class="far fa-frown mr-1"></i> কোনো ফলাফল পাওয়া যায়নি
      </div>
    </div>
  </div>

  <div class="card-footer d-flex justify-content-between align-items-center">
    <small class="text-muted">আপনার প্রশ্নের উত্তর না পেলে সাপোর্টে জানান।</small>
    <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#supportModal">
      <i class="fas fa-headset mr-1"></i> Contact Support
    </button>
  </div>
</div>


<script>
  // Tooltip
  $(function(){ $('[data-toggle="tooltip"]').tooltip(); });

  // Expand / Collapse all
  $('#faqExpandAll').on('click', function(){
    $('#faqAccordion .collapse').collapse('show');
  });
  $('#faqCollapseAll').on('click', function(){
    $('#faqAccordion .collapse').collapse('hide');
  });

  // Search filter
  const $faqSearch = $('#faqSearch');
  const $items = $('#faqAccordion .faq-item');
  const $nores = $('#faqNoResult');

  $faqSearch.on('input', function(){
    const q = $(this).val().toLowerCase().trim();
    let visible = 0;
    $items.each(function(){
      const text = $(this).text().toLowerCase();
      const match = !q || text.indexOf(q) !== -1;
      $(this).toggle(match);
      if (match) visible++;
    });
    $nores.toggleClass('d-none', visible > 0);
    // চাইলে ম্যাচ পাওয়া আইটেমগুলো অটো-এক্সপ্যান্ড করুন:
    if (q) {
      $('#faqAccordion .collapse').collapse('hide');
      $items.filter(':visible').find('.collapse').collapse('show');
    }
  });
</script>
