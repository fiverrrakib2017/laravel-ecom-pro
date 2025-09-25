window.load_hotspot_profiles = function(routerId, url) {
    const $profile = $('#hotspot_profile_id');
    $profile.prop('disabled', true).html('<option>Loading...</option>');
        if (!routerId) {
        $profile.html('<option value="">-- Select Profile --</option>').prop('disabled', true);
        return;
    }
    var link_url = url.replace(':id', routerId);
    $.ajax({
        url: link_url,
        type: 'GET',
        dataType: 'json',
        headers: { 'Accept': 'application/json' },
        success: function(res) {
            let opts = '<option value="">-- Select Profile --</option>';
            if (res && res.success) {
                (res.profiles || []).forEach(function(p) {
                opts += '<option value="' + p.id + '">' + p.name + ' (' + p.mikrotik_profile + ')</option>';
                });
            }
            $profile.html(opts).prop('disabled', false);
        },
        error: function() {
            $profile.html('<option value="">-- Select Profile --</option>').prop('disabled', false);
            toastr.error('Could not load profiles.');
        }
    });
};
