@extends('Backend.Layout.App')
@section('title', 'Mikrotik Logs | Admin Panel')
@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card bg-dark text-white">
            <div class="card-header border-bottom border-secondary">
                <i class="fas fa-terminal"></i> Mikrotik Log Terminal
            </div>
            <div class="card-body p-3 terminal-log" id="logTerminal"
                style="background-color: #0d0d0d; font-family: 'Courier New', monospace; font-size: 14px; max-height: 600px; overflow-y: auto;">

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
                        <span class="text-primary">{{ $log['router_name'] }}</span> :
                        <span class="{{ $isError ? 'text-danger fw-bold' : 'text-light' }}">
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
    <script type="text/javascript">


    document.addEventListener("DOMContentLoaded", function () {
        const terminal = document.getElementById("logTerminal");
        terminal.scrollTop = terminal.scrollHeight;
    });
    </script>
@endsection
