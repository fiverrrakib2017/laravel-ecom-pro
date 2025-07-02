@extends('Backend.Layout.App')
@section('title', 'Mikrotik Logs | Admin Panel')
@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card bg-dark text-white">
            <div class="card-header border-bottom border-secondary">
                <i class="fas fa-terminal"></i> Mikrotik Log Terminal
            </div>
            <div class="card-body p-3" style="background-color: #121212; font-family: monospace; font-size: 14px; max-height: 600px; overflow-y: auto;">
               @foreach ($allLogs as $log)
                    @php
                    $isError = false;
                    $errorKeywords = ['error', 'fail', 'invalid', 'timeout', 'disconnected'];
                    foreach ($errorKeywords as $word) {
                        if (stripos($log['topics'], $word) !== false || stripos($log['message'], $word) !== false) {
                            $isError = true;
                            break;
                        }
                    }
                    @endphp

                    <div style="margin-bottom: 6px;">
                        <span class="text-success">[{{ $log['time'] }}]</span>
                        <span class="text-warning">({{ strtoupper($log['topics']) }})</span>
                        <span class="text-info">{{ $log['router_name'] }}</span> :
                        <span class="{{ $isError ? 'text-danger' : 'text-white' }}">
                            {{ $log['message'] }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection


@section('script')
    <script>
        $(document).ready(function() {
            $('#customers_log_datatable1').DataTable();
        });
    </script>
@endsection
