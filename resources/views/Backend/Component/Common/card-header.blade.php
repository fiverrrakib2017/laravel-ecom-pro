  <style>
      .card-header-pro {
          position: relative;
          overflow: hidden;
          padding: 16px 20px;
          border: 0;
          color: #fff;
          background: linear-gradient(135deg, #17a2b8 0%, #0ea5e9 45%, #2563eb 100%);
          box-shadow: inset 0 -1px 0 rgba(255, 255, 255, .2);
          border-top-left-radius: .25rem;
          border-top-right-radius: .25rem;
      }

      .card-header-pro::after {
          content: "";
          position: absolute;
          right: -30px;
          top: -30px;
          width: 180px;
          height: 180px;
          pointer-events: none;
          background: radial-gradient(circle at 30% 30%,
                  rgba(255, 255, 255, .35), rgba(255, 255, 255, 0) 60%);
          transform: rotate(25deg);
          opacity: .7;
      }

      .card-header-pro .card-title {
          font-weight: 700;
          letter-spacing: .2px;
      }

      .card-header-pro .icon-badge {
          width: 44px;
          height: 44px;
          border-radius: 12px;
          display: inline-flex;
          align-items: center;
          justify-content: center;
          background: rgba(255, 255, 255, .15);
          color: #fff;
          box-shadow: 0 6px 16px rgba(0, 0, 0, .08), inset 0 0 0 1px rgba(255, 255, 255, .25);
      }

      .card-header-pro .subtitle {
          display: block;
          margin-top: 2px;
          font-size: .825rem;
      }

      .btn-header {
          background: rgba(255, 255, 255, .16);
          border: 1px solid rgba(255, 255, 255, .25);
          color: #fff;
      }

      .btn-header:hover {
          background: rgba(255, 255, 255, .25);
          color: #fff;
      }

      /* Inline validation helper */
      .invalid-feedback {
          display: block;
      }
  </style>

  <div class="card-header card-header-pro d-flex align-items-center justify-content-between">
      <div class="d-flex align-items-center">
         <span class="icon-badge mr-2">
            {!! $icon !!}
        </span>
        <div class="lh-1">
            <h4 class="card-title m-0">{{ $title }}</h4><br>
            <small class="subtitle text-white-50">{{ $description }}</small>
        </div>

      </div>
      <div class="header-actions d-none d-md-flex">
            <button type="button" id="btn-refresh" class="btn btn-header btn-sm mr-2">
                <i class="fas fa-sync-alt"></i> Refresh
            </button>
            <button type="button" onclick="window.location='{{ route('admin.settings.information.index') }}'" class="btn btn-header btn-sm mr-2">
                <i class="fas fa-cog"></i> Settings
            </button>
            <button type="button" onclick="history.back()" class="btn btn-header btn-sm mr-2">
            <i class="fas fa-arrow-left"></i> Back
        </button>

           {!! $button ?? '' !!}
      </div>
  </div>



  <script type="text/javascript">
      $(function() {
          /*--------- Refresh button behavior--------------*/
          $(document).on('click', '#btn-refresh', function() {
              var $btn = $(this);
              var html0 = $btn.html();

              $btn.prop('disabled', true)
                  .html('<i class="fas fa-sync-alt fa-spin"></i> Refreshing...');

              setTimeout(function() {
                  toastr.success('Refresh Completed');
              }, 1000);
              setTimeout(function() {
                  var url = new URL(window.location.href);
                  url.searchParams.set('_r', Date.now().toString());
                  window.location.replace(url.toString());
              }, 2000);
          });
      });
  </script>
