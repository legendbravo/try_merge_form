<div class="notification-bell-wrapper position-relative d-inline-block me-3">
    <button id="notificationBell" class="btn btn-link position-relative p-0" style="font-size: 1.7rem; color: #0f2754;">
        <i class="fas fa-bell"></i>
        <span id="notificationBadge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="display: none;">0</span>
    </button>
    <div id="notificationDropdown" class="dropdown-menu shadow p-0 mt-2" style="min-width: 350px; max-width: 400px; left: auto; right: 0; display: none;">
        <div class="dropdown-header bg-light fw-bold py-2 px-3 border-bottom">
            Notifications
        </div>
        <div id="notificationList" style="max-height: 350px; overflow-y: auto;">
            <div class="text-center py-3 text-muted">Loading...</div>
        </div>
    </div>
</div>

@push('styles')
<style>
.notification-bell-wrapper { z-index: 1051; }
#notificationBell:focus { outline: none; box-shadow: none; }
#notificationDropdown { border-radius: 12px; left: auto !important; right: 0 !important; }
.notification-item { cursor: pointer; transition: background 0.2s; }
.notification-item.unread { background: #f6f6f7; }
.notification-item:hover { background: #e9ecef; }
.notification-message { color: #0f2754; font-weight: 500; }
.notification-timestamp { font-size: 0.85rem; color: #6c757d; }
</style>
@endpush

@push('scripts')
<script>
$(function() {
    let dropdownVisible = false;
    const bell = $('#notificationBell');
    const dropdown = $('#notificationDropdown');
    const badge = $('#notificationBadge');
    const list = $('#notificationList');

    function fetchNotifications(markAsRead = false) {
        $.get("{{ route('barangay.notifications.index') }}", function(data) {
            if (data.notifications && data.notifications.length > 0) {
                let html = '';
                let unreadCount = 0;
                data.notifications.forEach(function(n) {
                    let unread = n.is_read ? '' : 'unread';
                    if (!n.is_read) unreadCount++;
                    html += `<a href="#" class="notification-item ${unread}" data-id="${n.id}" data-report="${n.report_id}" data-message="${n.message.replace(/\"/g, '&quot;')}" data-url="${n.url}" style="text-decoration:none;">
                        <div class="d-flex align-items-center px-3 py-2">
                            <div class="flex-grow-1">
                                <div class="notification-message">${n.message}</div>
                                <div class="notification-timestamp">${n.time_ago}</div>
                            </div>
                        </div>
                    </a>`;
                });
                list.html(html);
                if (unreadCount > 0) {
                    badge.text(unreadCount).show();
                } else {
                    badge.hide();
                }
            } else {
                list.html('<div class="text-center py-3 text-muted">No notifications</div>');
                badge.hide();
            }
        });
        if (markAsRead) {
            $.post("{{ route('barangay.notifications.markAsRead') }}", {_token: '{{ csrf_token() }}'});
        }
    }

    bell.on('click', function(e) {
        e.stopPropagation();
        dropdown.toggle();
        dropdownVisible = !dropdownVisible;
        if (dropdownVisible) {
            fetchNotifications(true);
        }
    });

    $(document).on('click', function(e) {
        if (!$(e.target).closest('.notification-bell-wrapper').length) {
            dropdown.hide();
            dropdownVisible = false;
        }
    });

    list.on('click', '.notification-item', function(e) {
        e.preventDefault();
        var url = $(this).data('url');
        if (url && url !== '#') {
            window.location.href = url;
        }
    });

    // Initial fetch
    fetchNotifications();
    // Poll every 60 seconds
    setInterval(fetchNotifications, 60000);
});
</script>
@endpush

<!-- Notification Modal -->
<div class="modal fade" id="notificationModal" tabindex="-1" aria-labelledby="notificationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="notificationModalLabel">Notification</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="notificationModalMessage"></div>
            </div>
            <div class="modal-footer">
                <a href="#" id="notificationModalGoTo" class="btn btn-primary" target="_blank">Go to Submission</a>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div> 