<?php require_once VIEWS_PATH . '/layouts/header.php'; ?>

<?= breadcrumbs([['label' => 'Dashboard', 'url' => '?page=dashboard'], ['label' => 'Calendar', 'url' => '']]) ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0"><i class="bi bi-calendar3 me-2"></i>Calendar</h4>
    <div class="d-flex gap-2">
        <span class="badge" style="background:#3B82F6;">Leases</span>
        <span class="badge" style="background:#10B981;">Paid</span>
        <span class="badge" style="background:#EF4444;">Overdue</span>
        <span class="badge" style="background:#F59E0B;">Maintenance / Pending</span>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div id="calendar"></div>
    </div>
</div>

<!-- FullCalendar CDN -->
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.9/index.global.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,dayGridWeek,listWeek'
        },
        events: '?page=calendar&action=events',
        eventClick: function(info) {
            if (info.event.url) {
                window.location.href = info.event.url;
                info.jsEvent.preventDefault();
            }
        },
        height: 'auto',
        themeSystem: 'standard',
        nowIndicator: true,
        dayMaxEvents: 3,
        eventDisplay: 'block'
    });
    calendar.render();
});
</script>

<?php require_once VIEWS_PATH . '/layouts/footer.php'; ?>
