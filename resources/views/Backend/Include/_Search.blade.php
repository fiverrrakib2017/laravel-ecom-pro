<!-- SidebarSearch Form -->
<div class="form-inline">
    <div class="input-group" data-widget="sidebar-search">
        <input id="search_input" class="form-control form-control-sidebar" type="search" placeholder="Search by Name, ID, or Phone" aria-label="Search">
        <div class="input-group-append">
            <button class="btn btn-sidebar" id="search_button">
                <i class="fas fa-search fa-fw"></i>
            </button>
        </div>
    </div>
</div>

<!-- Search Results -->
<ul id="search_results" class="list-group" style="display: none;">
    <!-- Dynamic search results will be appended here -->
</ul>

<script src="{{ asset('Backend/plugins/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('Backend/plugins/select2/js/select2.full.min.js') }}"></script>
<script>
    $(document).ready(function() {
        $('#search_input').on('input', function() {
            var search_term = $(this).val();

            if (search_term.length >= 2) {
                $.ajax({
                    url: "{{ route('admin.customer.search') }}",
                    method: 'GET',
                    data: { query: search_term },
                    success: function(response) {
                        var results = response.data;
                        var resultList = $('#search_results');
                        resultList.empty();

                        if (results.length > 0) {
                            resultList.show();
                            results.forEach(function(user) {

                                var statusClass = user.status === 'online' ? 'online' : 'offline';
                                var statusIcon = '';
                                var last_seen = '';
                                var viewUrl = '{{ route('admin.customer.view', ':id') }}'.replace(':id', user.id);

                                if (user.status === 'online') {
                                    statusIcon = '<i class="fas fa-unlock" style="font-size:15px;color:green;margin-right:8px;" title="Online"></i>';
                                } else if (user.status === 'offline') {
                                    statusIcon = '<i class="fas fa-lock" style="font-size:15px;color:red;margin-right:8px;" title="Offline"></i>';
                                    if (user.last_seen) {
                                        last_seen = `<small style="color:gray;margin-top:2px;">(${__time_ago(user.last_seen)})</small>`;
                                    }
                                } else if (user.status === 'expired') {
                                    statusIcon = '<i class="fas fa-clock" style="font-size:15px;color:orange;margin-right:8px;" title="Expired"></i>';
                                } else if (user.status === 'blocked') {
                                    statusIcon = '<i class="fas fa-ban" style="font-size:15px;color:darkred;margin-right:8px;" title="Blocked"></i>';
                                } else if (user.status === 'disabled') {
                                    statusIcon = '<i class="fas fa-user-slash" style="font-size:15px;color:gray;margin-right:8px;" title="Disabled"></i>';
                                } else if (user.status === 'discontinue') {
                                    statusIcon = '<i class="fas fa-times-circle" style="font-size:15px;color:#ff6600;margin-right:8px;" title="Discontinue"></i>';
                                }

                                resultList.append(`
                                    <li class="list-group-item search-result-item" data-id="${user.id}">
                                        <a href="${viewUrl}" style="display:flex;align-items:center;text-decoration:none;color:#333;">
                                            ${statusIcon}
                                            <span style="display:flex;flex-direction:column;line-height:1.1;">
                                                <span style="font-size:16px;font-weight:bold;">${user.fullname}</span>
                                                ${last_seen}
                                                <small>User ID: ${user.id}</small> <small>Phone: ${user.phone}</small>
                                            </span>
                                        </a>
                                    </li>
                                `);
                            });
                        } else {
                            resultList.hide();
                        }
                    },
                    error: function() {
                        console.error('Error fetching search results');
                    }
                });
            } else {
                $('#search_results').hide();
            }
        });

        $('#search_results').on('click', '.search-result-item', function() {
            var customer_id = $(this).data('id');
            window.location.href = "{{ route('admin.customer.view', ':id') }}".replace(':id', customer_id);
        });
    });
</script>
<style>
.search-results-container {
    padding: 0;
    margin-top: 10px;
    max-height: 300px;
    overflow-y: auto;
}

.search-result-item {
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 5px;
    margin-bottom: 5px;
    transition: background-color 0.3s ease;
}

.search-result-item:hover {
    background-color: #f1f1f1;
}

.search-result-link {
    display: flex;
    align-items: center;
    text-decoration: none;
    color: #333;
}

.search-result-link .user-info {
    margin-left: 10px;
    font-size: 14px;
}

.user-name {
    font-weight: bold;
    font-size: 16px;
    color: #333;
}

.user-meta {
    font-size: 12px;
    color: #6c757d;
    margin-top: 5px;
}

.user-meta .user-id, .user-meta .user-phone {
    margin-right: 10px;
}

.last-seen {
    font-size: 12px;
    color: #888;
}

/* Status Icons */
.online-status {
    color: green;
}

.offline-status {
    color: red;
}

.expired-status {
    color: orange;
}

.blocked-status {
    color: darkred;
}

/* Optional Styling for Hover Effect */
.search-result-item:hover .user-name {
    color: #007bff;
}
</style>
