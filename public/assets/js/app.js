document.addEventListener('DOMContentLoaded', function () {

    // ===== DARK MODE TOGGLE =====
    const themeToggle = document.getElementById('themeToggle');
    const htmlEl = document.documentElement;

    // Restore saved theme
    const savedTheme = localStorage.getItem('theme') || 'light';
    htmlEl.setAttribute('data-theme', savedTheme);
    updateThemeIcon(savedTheme);

    if (themeToggle) {
        themeToggle.addEventListener('click', function () {
            const current = htmlEl.getAttribute('data-theme');
            const next = current === 'dark' ? 'light' : 'dark';
            htmlEl.setAttribute('data-theme', next);
            localStorage.setItem('theme', next);
            updateThemeIcon(next);
        });
    }

    function updateThemeIcon(theme) {
        if (!themeToggle) return;
        const icon = themeToggle.querySelector('i');
        if (icon) {
            icon.className = theme === 'dark' ? 'bi bi-sun-fill' : 'bi bi-moon-fill';
        }
    }

    // ===== SIDEBAR COLLAPSE (Desktop) =====
    const sidebar = document.getElementById('sidebar');
    const collapseBtn = document.getElementById('sidebarCollapseBtn');
    const sidebarBackdrop = document.getElementById('sidebarBackdrop');
    const sidebarToggle = document.getElementById('sidebarToggle');

    // Restore collapsed state
    if (localStorage.getItem('sidebarCollapsed') === 'true' && sidebar) {
        sidebar.classList.add('collapsed');
        document.body.classList.add('sidebar-collapsed');
    }

    if (collapseBtn && sidebar) {
        collapseBtn.addEventListener('click', function () {
            sidebar.classList.toggle('collapsed');
            document.body.classList.toggle('sidebar-collapsed');
            localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed'));
        });
    }

    // ===== SIDEBAR MOBILE TOGGLE =====
    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', function () {
            sidebar.classList.toggle('show');
            if (sidebarBackdrop) sidebarBackdrop.classList.toggle('show');
        });
    }

    if (sidebarBackdrop) {
        sidebarBackdrop.addEventListener('click', function () {
            if (sidebar) sidebar.classList.remove('show');
            sidebarBackdrop.classList.remove('show');
        });
    }

    // ===== PAGE LOADER =====
    const pageLoader = document.getElementById('pageLoader');

    // Show loader on navigation
    document.addEventListener('click', function (e) {
        const link = e.target.closest('a[href]');
        if (link && !link.getAttribute('href').startsWith('#') &&
            !link.getAttribute('href').startsWith('javascript') &&
            !link.hasAttribute('data-bs-toggle') &&
            !link.classList.contains('no-loader') &&
            link.target !== '_blank') {
            if (pageLoader) pageLoader.classList.add('active');
        }
    });

    // Hide loader when page is ready
    if (pageLoader) pageLoader.classList.remove('active');

    // ===== INITIALIZE DATATABLES =====
    if (typeof $.fn.DataTable !== 'undefined') {
        document.querySelectorAll('.data-table').forEach(function (table) {
            if (!$.fn.DataTable.isDataTable(table)) {
                $(table).DataTable({
                    pageLength: 15,
                    order: [],
                    language: { search: '', searchPlaceholder: 'Search...' },
                    dom: '<"row"<"col-sm-6"l><"col-sm-6"f>>rtip'
                });
            }
        });
    }

    // ===== DELETE CONFIRMATION MODAL =====
    const deleteModal = document.getElementById('deleteModal');
    if (deleteModal) {
        deleteModal.addEventListener('show.bs.modal', function (event) {
            const trigger = event.relatedTarget;
            const itemName = trigger.getAttribute('data-item-name') || 'this item';
            const form = trigger.closest('.delete-form');

            document.getElementById('deleteItemName').textContent = itemName;

            const confirmBtn = document.getElementById('deleteConfirmBtn');
            const newBtn = confirmBtn.cloneNode(true);
            confirmBtn.parentNode.replaceChild(newBtn, confirmBtn);

            newBtn.addEventListener('click', function () {
                if (form) form.submit();
            });
        });
    }

    // ===== GLOBAL SEARCH =====
    const searchInput = document.getElementById('globalSearch');
    const searchResults = document.getElementById('searchResults');
    let searchTimeout;

    if (searchInput && searchResults) {
        searchInput.addEventListener('input', function () {
            clearTimeout(searchTimeout);
            const q = this.value.trim();
            if (q.length < 2) {
                searchResults.classList.add('d-none');
                return;
            }

            searchTimeout = setTimeout(async () => {
                try {
                    const res = await fetch('?page=search&q=' + encodeURIComponent(q));
                    const data = await res.json();

                    if (data.length === 0) {
                        searchResults.innerHTML = '<div class="px-3 py-2" style="font-size:0.85rem;color:var(--color-text-muted);">No results found</div>';
                    } else {
                        const typePages = { property: 'properties', unit: 'units', tenant: 'tenants', lease: 'leases' };
                        searchResults.innerHTML = data.map(function (r) {
                            var page = typePages[r.type] || r.type + 's';
                            return '<a href="?page=' + page + '&action=show&id=' + r.id + '" class="d-flex align-items-center gap-2 px-3 py-2 text-decoration-none search-result-item" style="color:var(--color-text);font-size:0.85rem;">' +
                                '<i class="' + (r.icon || 'bi bi-search') + '" style="color:var(--color-text-muted);"></i>' +
                                '<span>' + escapeHtml(r.name) + '</span>' +
                                '<span class="badge-status ms-auto" style="font-size:0.7rem;">' + r.type + '</span>' +
                                '</a>';
                        }).join('');
                    }
                    searchResults.classList.remove('d-none');
                } catch (e) {
                    console.error('Search error:', e);
                }
            }, 300);
        });

        // Close on click outside
        document.addEventListener('click', function (e) {
            if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
                searchResults.classList.add('d-none');
            }
        });

        // Close on Escape
        searchInput.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                searchResults.classList.add('d-none');
                searchInput.blur();
            }
        });
    }
});

// ===== TOAST NOTIFICATIONS =====
function showToast(message, type) {
    type = type || 'info';
    var container = document.getElementById('toastContainer');
    if (!container) return;

    var icons = {
        success: 'bi-check-circle-fill',
        error: 'bi-x-circle-fill',
        warning: 'bi-exclamation-triangle-fill',
        info: 'bi-info-circle-fill'
    };

    var toast = document.createElement('div');
    toast.className = 'toast-notification toast-' + type;
    toast.innerHTML =
        '<i class="bi ' + (icons[type] || icons.info) + ' toast-icon"></i>' +
        '<span>' + escapeHtml(message) + '</span>' +
        '<button class="toast-close" onclick="this.parentElement.remove()">&times;</button>';

    container.appendChild(toast);

    // Auto-dismiss after 5s
    setTimeout(function () {
        toast.classList.add('removing');
        setTimeout(function () { toast.remove(); }, 300);
    }, 5000);
}

// ===== UTILITY: Escape HTML =====
function escapeHtml(str) {
    if (!str) return '';
    var div = document.createElement('div');
    div.appendChild(document.createTextNode(str));
    return div.innerHTML;
}
