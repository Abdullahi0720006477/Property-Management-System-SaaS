/**
 * Global Search - debounced search with dropdown results and keyboard navigation
 */
document.addEventListener('DOMContentLoaded', function () {
    var searchInput = document.getElementById('globalSearch');
    var searchResults = document.getElementById('searchResults');
    if (!searchInput || !searchResults) return;

    var debounceTimer = null;
    var activeIndex = -1;
    var currentResults = [];

    // URL map for result types
    var typeUrls = {
        property: '?page=properties&action=view&id=',
        unit: '?page=units&action=view&id=',
        tenant: '?page=tenants&action=view&id=',
        lease: '?page=leases&action=view&id='
    };

    var typeLabels = {
        property: 'Property',
        unit: 'Unit',
        tenant: 'Tenant',
        lease: 'Lease'
    };

    function escapeHtml(str) {
        var div = document.createElement('div');
        div.appendChild(document.createTextNode(str));
        return div.innerHTML;
    }

    function showResults(results) {
        currentResults = results;
        activeIndex = -1;

        if (results.length === 0) {
            searchResults.innerHTML = '<div class="search-no-results">No results found</div>';
            searchResults.classList.add('show');
            return;
        }

        var html = '';
        for (var i = 0; i < results.length; i++) {
            var r = results[i];
            var url = typeUrls[r.type] ? typeUrls[r.type] + r.id : '#';
            html += '<a href="' + url + '" class="search-result-item" data-index="' + i + '">' +
                '<i class="bi ' + escapeHtml(r.icon) + ' search-result-icon"></i>' +
                '<span class="search-result-name">' + escapeHtml(r.name) + '</span>' +
                '<span class="search-result-type">' + escapeHtml(typeLabels[r.type] || r.type) + '</span>' +
                '</a>';
        }

        searchResults.innerHTML = html;
        searchResults.classList.add('show');
    }

    function hideResults() {
        searchResults.classList.remove('show');
        searchResults.innerHTML = '';
        activeIndex = -1;
        currentResults = [];
    }

    function updateActive() {
        var items = searchResults.querySelectorAll('.search-result-item');
        for (var i = 0; i < items.length; i++) {
            items[i].classList.toggle('active', i === activeIndex);
        }
        // Scroll active item into view
        if (activeIndex >= 0 && items[activeIndex]) {
            items[activeIndex].scrollIntoView({ block: 'nearest' });
        }
    }

    function performSearch(query) {
        if (query.length < 2) {
            hideResults();
            return;
        }

        var xhr = new XMLHttpRequest();
        xhr.open('GET', '?page=search&q=' + encodeURIComponent(query), true);
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4 && xhr.status === 200) {
                try {
                    var data = JSON.parse(xhr.responseText);
                    showResults(data);
                } catch (e) {
                    hideResults();
                }
            }
        };
        xhr.send();
    }

    // Debounced input handler
    searchInput.addEventListener('input', function () {
        var query = this.value.trim();
        clearTimeout(debounceTimer);

        if (query.length < 2) {
            hideResults();
            return;
        }

        debounceTimer = setTimeout(function () {
            performSearch(query);
        }, 300);
    });

    // Keyboard navigation
    searchInput.addEventListener('keydown', function (e) {
        var items = searchResults.querySelectorAll('.search-result-item');
        if (!items.length) return;

        if (e.key === 'ArrowDown') {
            e.preventDefault();
            activeIndex = activeIndex < items.length - 1 ? activeIndex + 1 : 0;
            updateActive();
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            activeIndex = activeIndex > 0 ? activeIndex - 1 : items.length - 1;
            updateActive();
        } else if (e.key === 'Enter') {
            e.preventDefault();
            if (activeIndex >= 0 && items[activeIndex]) {
                window.location.href = items[activeIndex].getAttribute('href');
            }
        } else if (e.key === 'Escape') {
            hideResults();
            searchInput.blur();
        }
    });

    // Focus shows results if there's input
    searchInput.addEventListener('focus', function () {
        if (this.value.trim().length >= 2) {
            performSearch(this.value.trim());
        }
    });

    // Click outside to close
    document.addEventListener('click', function (e) {
        var wrapper = searchInput.closest('.global-search-wrapper');
        if (wrapper && !wrapper.contains(e.target)) {
            hideResults();
        }
    });

    // Hover sets active index
    searchResults.addEventListener('mouseover', function (e) {
        var item = e.target.closest('.search-result-item');
        if (item) {
            var idx = parseInt(item.getAttribute('data-index'), 10);
            if (!isNaN(idx)) {
                activeIndex = idx;
                updateActive();
            }
        }
    });
});
