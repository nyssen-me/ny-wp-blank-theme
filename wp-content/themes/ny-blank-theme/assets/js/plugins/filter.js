/* =============================================================================
   Events Filter — client-side filtering for page-events.php
   Reads event data from <script id="events-data" type="application/json">.
   Filters by: text search, Flatpickr date range, event_cat taxonomy.
   No page reloads or AJAX — DOM items are shown/hidden via [hidden].
   ========================================================================== */

(function () {
    'use strict';

    // ============================================================
    // BOOT — bail early if not on the events page
    // ============================================================
    var dataEl = document.getElementById('events-data');
    if (!dataEl) return;

    var eventsData;
    try {
        eventsData = JSON.parse(dataEl.textContent || dataEl.innerHTML);
    } catch (e) {
        return;
    }

    // ============================================================
    // CONFIG
    // ============================================================
    var PAGE_SIZE = 20;

    // ============================================================
    // STATE
    // ============================================================
    var state = {
        searchText: '',
        dateRange:  null,    // [startStr, endStr] ('Y-m-d'), or null
        eventType:  [],      // array of event_cat slugs
        page:       PAGE_SIZE
    };

    // ============================================================
    // DOM REFERENCES
    // ============================================================
    var fp             = null;
    var form           = document.querySelector('[data-live="true"]');
    var eventsList     = document.getElementById('events');
    var statusWrap     = document.querySelector('[data-form-status]');
    var statusMsg      = document.querySelector('[data-status-message]');
    var datepickerBtn  = document.getElementById('datepicker-button');
    var datepickerDlg  = document.getElementById('datepicker-dialog');
    var dateInput      = document.getElementById('date-picker');
    var btnText        = document.querySelector('.filter-datepicker-button .button-text');
    var searchInput    = document.getElementById('event-search');
    var typeSelect     = document.getElementById('event-type-select');
    var loadMoreBtn    = document.getElementById('events-load-more');

    // ============================================================
    // INIT
    // ============================================================
    function init() {
        if (!form || !eventsList) return;

        initFlatpickr();
        initMultiSelect();
        bindSearchInput();
        bindDialogControls();
        bindResetButton();
        bindLoadMore();
        preventFormSubmit();
        applyFilters();
    }

    // ============================================================
    // FLATPICKR DATE RANGE
    // ============================================================
    function initFlatpickr() {
        if (typeof flatpickr === 'undefined' || !dateInput) return;

        fp = flatpickr(dateInput, {
            mode:           'range',
            dateFormat:     'Y-m-d',
            minDate:        'today',
            inline:         true,
            disableMobile:  true,
            locale:         { firstDayOfWeek: 1 },
            onChange: function (selectedDates) {
                if (selectedDates.length === 2) {
                    state.dateRange = [dateToStr(selectedDates[0]), dateToStr(selectedDates[1])];
                    setButtonText(formatSelectedDates(state.dateRange));
                } else if (selectedDates.length === 1) {
                    var single = dateToStr(selectedDates[0]);
                    state.dateRange = [single, single];
                    setButtonText(formatSelectedDates(state.dateRange));
                } else {
                    state.dateRange = null;
                    setButtonText(null);
                }
                applyFilters();
            }
        });
    }

    // ============================================================
    // INPUT BINDINGS
    // ============================================================
    function bindSearchInput() {
        if (!searchInput) return;
        searchInput.addEventListener('input', function () {
            state.searchText = this.value.trim().toLowerCase();
            applyFilters();
        });
    }

    function initMultiSelect() {
        if (typeof jQuery === 'undefined' || typeof jQuery.fn.multipleSelect === 'undefined' || !typeSelect) return;
        jQuery('#event-type-select').multipleSelect({
            placeholder: 'Event type',
            selectAll: false,
            animate: 'fade'
        }).on('change', function () {
            state.eventType = jQuery('#event-type-select').multipleSelect('getSelects');
            applyFilters();
        });
    }

    function bindDialogControls() {
        if (!datepickerBtn || !datepickerDlg) return;

        datepickerBtn.addEventListener('click', function () {
            positionDialog();
            datepickerDlg.showModal();
            adjustDialogPosition();
            window.addEventListener('scroll', onDialogScroll, { passive: true });
        });

        datepickerDlg.addEventListener('click', function (e) {
            if (e.target === datepickerDlg) datepickerDlg.close();
        });

        datepickerDlg.querySelectorAll('[data-dialog-close]').forEach(function (btn) {
            btn.addEventListener('click', function () { datepickerDlg.close(); });
        });

        datepickerDlg.addEventListener('close', function () {
            clearDialogPosition();
            window.removeEventListener('scroll', onDialogScroll);
        });
    }

    function positionDialog() {
        var rect = datepickerBtn.getBoundingClientRect();
        datepickerDlg.style.margin = '0';
        datepickerDlg.style.top    = (rect.bottom + 8) + 'px';
        datepickerDlg.style.left   = rect.left + 'px';
    }

    function adjustDialogPosition() {
        var dlgRect = datepickerDlg.getBoundingClientRect();
        if (dlgRect.right > window.innerWidth - 8) {
            datepickerDlg.style.left = Math.max(8, window.innerWidth - dlgRect.width - 8) + 'px';
        }
    }

    function onDialogScroll() {
        positionDialog();
        adjustDialogPosition();
    }

    function clearDialogPosition() {
        datepickerDlg.style.top    = '';
        datepickerDlg.style.left   = '';
        datepickerDlg.style.margin = '';
    }

    function bindResetButton() {
        var resetBtn = form && form.querySelector('[type="reset"]');
        if (!resetBtn) return;
        resetBtn.addEventListener('click', function (e) {
            e.preventDefault();
            resetAllFilters();
        });
    }

    function bindLoadMore() {
        if (!loadMoreBtn) return;
        loadMoreBtn.addEventListener('click', function () {
            state.page += PAGE_SIZE;
            applyFilters();
        });
    }

    function preventFormSubmit() {
        if (form) {
            form.addEventListener('submit', function (e) { e.preventDefault(); });
        }
    }

    // ============================================================
    // FILTER LOGIC
    // ============================================================
    function applyFilters() {
        var items      = eventsList.querySelectorAll('li[data-id]');
        var hasFilters = !!(state.searchText || state.dateRange || state.eventType.length);
        var count      = 0;

        if (hasFilters) {
            // Filter mode: show all matches, hide pagination
            items.forEach(function (item) {
                var id = item.dataset.id;
                var ev = eventsData.find(function (e) { return e.id === id; });
                var visible = ev ? matchesFilters(ev) : true;
                item.hidden = !visible;
                if (visible) count++;
            });
            updateLoadMore(false, 0);
        } else {
            // Paginated mode: show first state.page items
            items.forEach(function (item, index) {
                item.hidden = index >= state.page;
                if (!item.hidden) count++;
            });
            var remaining = items.length - state.page;
            updateLoadMore(remaining > 0, remaining);
        }

        updateStatusBar(hasFilters, count);
        eventsList.classList.toggle('filtered', hasFilters);
    }

    function matchesFilters(ev) {
        // Text search on title
        if (state.searchText) {
            var title = (ev.title || '').toLowerCase();
            if (title.indexOf(state.searchText) === -1) return false;
        }

        // Date selection — event overlaps the selected date range
        if (state.dateRange) {
            var rangeStart = state.dateRange[0];
            var rangeEnd   = state.dateRange[1];

            if (Array.isArray(ev.dates)) {
                // Repeater: any individual event date must fall within the selected range
                var hasMatch = ev.dates.some(function (d) {
                    var dStr = String(d).substring(0, 10);
                    return dStr >= rangeStart && dStr <= rangeEnd;
                });
                if (!hasMatch) return false;
            } else {
                // Single range (e_from / e_to): event range must overlap the selected range
                var eStartStr = String(ev.start_date || '').substring(0, 10);
                var eEndStr   = String(ev.end_date   || '').substring(0, 10);
                var overlaps  = eStartStr <= rangeEnd && eEndStr >= rangeStart;
                if (!overlaps) return false;
            }
        }

        // Event type (any selected category must be present)
        if (state.eventType.length > 0) {
            var cats     = ev.categories || [];
            var hasType  = state.eventType.some(function (t) { return cats.indexOf(t) !== -1; });
            if (!hasType) return false;
        }

        return true;
    }

    // ============================================================
    // STATUS BAR
    // ============================================================
    function updateStatusBar(hasFilters, count) {
        if (!statusWrap) return;

        if (!hasFilters) {
            statusWrap.classList.remove('active');
            return;
        }

        statusWrap.classList.add('active');
        if (!statusMsg) return;

        var html = '<span>Showing <strong>' + count + '</strong> event' + (count !== 1 ? 's' : '') + '</span> ';

        if (state.searchText) {
            html += makePill('“' + state.searchText + '”', 'search', '');
        }
        if (state.dateRange) {
            html += makePill(formatSelectedDates(state.dateRange), 'dateRange', '');
        }
        state.eventType.forEach(function (t) {
            var opt = typeSelect && typeSelect.querySelector('option[value="' + escapeAttr(t) + '"]');
            html += makePill(opt ? opt.textContent.trim() : t, 'eventType', t);
        });

        statusMsg.innerHTML = html;

        statusMsg.querySelectorAll('.btn--pill--filter').forEach(function (btn) {
            btn.addEventListener('click', function () {
                removeFilter(this.dataset.filter, this.dataset.value);
            });
        });
    }

    function updateLoadMore(show, remaining) {
        if (!loadMoreBtn) return;
        if (show) {
            loadMoreBtn.removeAttribute('hidden');
            loadMoreBtn.style.display = '';
            var next = Math.min(remaining, PAGE_SIZE);
            loadMoreBtn.textContent = 'Show ' + next + ' more event' + (next !== 1 ? 's' : '');
        } else {
            loadMoreBtn.style.display = 'none';
        }
    }

    function makePill(label, filterType, filterValue) {
        return '<button type="button" class="btn--pill btn--pill--filter" '
             + 'data-filter="' + filterType + '" data-value="' + escapeAttr(filterValue) + '">'
             + label + ' <span aria-hidden="true">&times;</span></button> ';
    }

    function escapeAttr(str) {
        return str ? str.replace(/"/g, '&quot;') : '';
    }

    // ============================================================
    // REMOVE A SINGLE FILTER / RESET ALL
    // ============================================================
    function removeFilter(filterType, filterValue) {
        switch (filterType) {
            case 'search':
                state.searchText = '';
                if (searchInput) searchInput.value = '';
                break;

            case 'dateRange':
                state.dateRange = null;
                if (fp) fp.clear();
                setButtonText(null);
                break;

            case 'eventType':
                state.eventType = state.eventType.filter(function (t) { return t !== filterValue; });
                if (typeof jQuery !== 'undefined' && jQuery.fn.multipleSelect) {
                    jQuery('#event-type-select').multipleSelect('setSelects', state.eventType);
                }
                break;
        }
        applyFilters();
    }

    function resetAllFilters() {
        state.searchText = '';
        state.dateRange  = null;
        state.eventType  = [];
        state.page       = PAGE_SIZE;

        if (searchInput) searchInput.value = '';
        if (fp) fp.clear();
        setButtonText(null);
        if (typeof jQuery !== 'undefined' && jQuery.fn.multipleSelect && typeSelect) {
            jQuery('#event-type-select').multipleSelect('uncheckAll');
        }

        applyFilters();
    }

    // ============================================================
    // HELPERS
    // ============================================================
    function setButtonText(text) {
        if (btnText) {
            btnText.textContent = text || (btnText.dataset.defaultText || 'Choose date(s)');
        }
    }

    function dateToStr(d) {
        var y   = d.getFullYear();
        var m   = String(d.getMonth() + 1).padStart(2, '0');
        var day = String(d.getDate()).padStart(2, '0');
        return y + '-' + m + '-' + day;
    }

    function formatSelectedDates(strs) {
        var opts = { day: 'numeric', month: 'short' };
        var format = function (s) {
            return new Date(s + 'T00:00:00').toLocaleDateString('en-GB', opts);
        };
        if (strs[0] === strs[1]) return format(strs[0]);
        return format(strs[0]) + ' – ' + format(strs[1]);
    }

    // ============================================================
    // BOOT
    // ============================================================
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

}());
