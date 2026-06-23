/* ====================================================
   Melcom Shop Audit – Web Mobile  |  app.js
   Replicates the APK logic: Login → Scan → Save → View
   Talks to the same PHP backend on the audit server.
   ==================================================== */

// ── Configuration ──────────────────────────────────
const API_BASE = '';   // Same origin – files sit alongside the PHP backend

const ENDPOINTS = {
    scan:   API_BASE + '/shop_audit.php',
    save:   API_BASE + '/upload_audit_shop.php',
    view:   API_BASE + '/shop_report.php'         // lowercase on the actual server
};

// ── State ──────────────────────────────────────────
let state = {
    username: '',
    zone: '',
    ip: '',
    currentItem: null,   // last scanned item object
    isKgs: false
};

// ── Helpers ────────────────────────────────────────
function $(id) { return document.getElementById(id); }

function showScreen(id) {
    document.querySelectorAll('.screen').forEach(s => s.classList.remove('active'));
    $(id).classList.add('active');
}

function toast(msg, type = 'success') {
    const el = $('toast');
    el.textContent = msg;
    el.className = 'toast ' + type;
    // force reflow
    void el.offsetWidth;
    el.classList.add('show');
    setTimeout(() => { el.classList.remove('show'); }, 2200);
}

// Detect client IP (best-effort via WebRTC is unreliable, so use a placeholder)
function getClientIP() {
    // The APK reads the WiFi IP. In a browser we can't do that.
    // We'll pass the user-agent or a placeholder – the PHP will also see REMOTE_ADDR.
    return 'WEB-CLIENT';
}

// ── LOGIN ──────────────────────────────────────────
function doLogin() {
    let username = $('login-username').value.trim().toUpperCase();
    let zone     = $('login-zone').value.trim().toUpperCase();

    // Handle wedge scanner pasting comma-separated values
    if (username.includes(',')) {
        const parts = username.split(',');
        if (parts.length >= 2) {
            username = parts[0].trim();
            zone = parts[1].trim();
        }
    }

    if (!username) { toast('Enter auditor name', 'error'); return; }
    if (!zone)     { toast('Enter zone / rack', 'error'); return; }

    state.username = username;
    state.zone     = zone;
    state.ip       = getClientIP();

    $('display-username').textContent = state.username;
    $('display-zone').textContent     = state.zone;
    $('display-ip').textContent       = state.ip;

    showScreen('screen-main');
    $('input-itemcode').focus();
}

// Allow Enter key to trigger login
$('login-username').addEventListener('keydown', e => { if (e.key === 'Enter') $('login-zone').focus(); });
$('login-zone').addEventListener('keydown', e => { if (e.key === 'Enter') doLogin(); });

// ── SCAN ───────────────────────────────────────────
$('input-itemcode').addEventListener('keydown', e => {
    if (e.key === 'Enter') doScan();
});

async function doScan() {
    const code = $('input-itemcode').value.trim();
    if (!code) { toast('Please scan or enter an item code', 'error'); return; }

    // Clear previous item
    clearItemCard();
    $('input-qty').value = '';
    $('input-qty').disabled = true;
    $('btn-save').disabled = true;
    $('unit-hint').classList.add('hidden');

    const scanBtn = document.querySelector('.btn-scan');
    scanBtn.classList.add('loading');

    const t0 = performance.now();

    try {
        const formData = new URLSearchParams();
        formData.append('item_code', code);

        const resp = await fetch(ENDPOINTS.scan, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: formData.toString()
        });

        const text = await resp.text();
        const t1 = performance.now();
        const ms = Math.round(t1 - t0);

        showTiming('timing-badge', 'timing-text', ms);

        let data;
        try { data = JSON.parse(text); } catch { data = []; }

        if (!data || data.length === 0) {
            toast('Item not found – check code', 'error');
            return;
        }

        // The PHP returns an array of arrays with both numeric and named keys
        const item = data[0];
        state.currentItem = {
            ITEM_CODE:  item.ITEM_CODE  || item.item_code  || item[0] || '',
            ITEM_NAME:  item.ITEM_NAME  || item.item_name  || item[1] || '',
            BARCODE:    item.BARCODE    || item.barcode    || item[2] || '',
            PRICE:      item.PRICE      || item.price      || item[3] || '',
            DEPT:       item.DEPT       || item.dept       || item[4] || '',
            SHOP_CODE:  item.SHOP_CODE  || item.shop_code  || item[5] || '',
            CURR_STOCK: item.CURR_STOCK || item.curr_stock || item[6] || '',
            UNIT:       item.UNIT       || item.unit       || item[7] || ''
        };

        populateItemCard(state.currentItem);

        // Determine KGS
        const unit = (state.currentItem.UNIT || '').toUpperCase();
        const name = (state.currentItem.ITEM_NAME || '').toUpperCase();
        state.isKgs = unit.includes('KGS') || unit.includes('KG')
                   || name.includes('KGS') || name.includes('KG ') || name.endsWith('KG');

        const qtyInput = $('input-qty');
        if (state.isKgs) {
            qtyInput.setAttribute('step', '0.1');
            qtyInput.setAttribute('inputmode', 'decimal');
            $('unit-hint').classList.remove('hidden');
        } else {
            qtyInput.setAttribute('step', '1');
            qtyInput.setAttribute('inputmode', 'numeric');
            $('unit-hint').classList.add('hidden');
        }

        qtyInput.disabled = false;
        $('btn-save').disabled = false;
        qtyInput.focus();

    } catch (err) {
        console.error('Scan error:', err);
        toast('Connection error – check WiFi', 'error');
    } finally {
        scanBtn.classList.remove('loading');
    }
}

function clearItemCard() {
    $('d-item-name').textContent = '—';
    $('d-price').textContent     = '—';
    $('d-shop').textContent      = '—';
    $('d-item-code').textContent  = '—';
    $('d-barcode').textContent   = '—';
    $('d-dept').textContent      = '—';
    $('d-stock').textContent     = '—';
    $('d-unit').textContent      = '—';
}

function populateItemCard(item) {
    $('d-item-name').textContent = item.ITEM_NAME || '—';
    $('d-price').textContent     = item.PRICE ? '₵ ' + item.PRICE : '—';
    $('d-shop').textContent      = item.SHOP_CODE || '—';
    $('d-item-code').textContent  = item.ITEM_CODE || '—';
    $('d-barcode').textContent   = item.BARCODE || '—';
    $('d-dept').textContent      = item.DEPT || '—';
    $('d-stock').textContent     = item.CURR_STOCK || '—';
    $('d-unit').textContent      = item.UNIT || '—';
}

function showTiming(badgeId, textId, ms) {
    $(badgeId).classList.remove('hidden');
    $(textId).textContent = ms + ' ms';
}

// ── CLEAR ──────────────────────────────────────────
function doClear() {
    $('input-itemcode').value = '';
    $('input-itemcode').focus();
    $('input-qty').disabled = true;
    $('input-qty').value = '';
    $('btn-save').disabled = true;
    clearItemCard();
    $('timing-badge').classList.add('hidden');
    $('unit-hint').classList.add('hidden');
    state.currentItem = null;
}

// ── SAVE ───────────────────────────────────────────
async function doSave() {
    const qtyVal = $('input-qty').value.trim();
    if (!qtyVal) { toast('Please enter a quantity', 'error'); return; }
    if (!state.currentItem) { toast('No item scanned', 'error'); return; }

    // Validate: non-KGS items must be whole numbers
    if (!state.isKgs && qtyVal.includes('.')) {
        toast('Whole numbers only for this item', 'error');
        return;
    }

    const saveBtn = $('btn-save');
    saveBtn.classList.add('loading');
    saveBtn.disabled = true;

    const t0 = performance.now();

    try {
        const formData = new URLSearchParams();
        formData.append('shop_code', state.currentItem.SHOP_CODE);
        formData.append('item_code', state.currentItem.ITEM_CODE);
        formData.append('qty',       qtyVal);
        formData.append('user',      state.username);
        formData.append('ip',        state.ip);
        formData.append('rack',      state.zone);

        const resp = await fetch(ENDPOINTS.save, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: formData.toString()
        });

        const text = await resp.text();
        const t1 = performance.now();
        const ms = Math.round(t1 - t0);

        if (text.toLowerCase().includes('sucess') || text.toLowerCase().includes('success')) {
            toast('Saved (' + ms + 'ms)', 'success');
        } else {
            toast('Save response: ' + text.substring(0, 60), 'error');
        }

        // Reset for next scan
        $('input-itemcode').value = '';
        $('input-qty').value = '';
        $('input-qty').disabled = true;
        clearItemCard();
        $('timing-badge').classList.add('hidden');
        $('unit-hint').classList.add('hidden');
        state.currentItem = null;
        $('input-itemcode').focus();

    } catch (err) {
        console.error('Save error:', err);
        toast('Connection error – check WiFi', 'error');
    } finally {
        saveBtn.classList.remove('loading');
        // keep save disabled until next scan
    }
}

// Allow Enter on QTY to trigger save
$('input-qty').addEventListener('keydown', e => {
    if (e.key === 'Enter') doSave();
});

// ── VIEW ───────────────────────────────────────────
async function showView() {
    showScreen('screen-view');

    const tbody = $('view-tbody');
    tbody.innerHTML = '<tr><td colspan="4" class="empty-row">Loading...</td></tr>';
    $('view-count').textContent = '…';
    $('view-timing').classList.add('hidden');

    const t0 = performance.now();

    try {
        const formData = new URLSearchParams();
        formData.append('username', state.username);
        formData.append('rack_num', state.zone);

        const resp = await fetch(ENDPOINTS.view, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: formData.toString()
        });

        const text = await resp.text();
        const t1 = performance.now();
        const ms = Math.round(t1 - t0);

        showTiming('view-timing', 'view-timing-text', ms);

        let data;
        try { data = JSON.parse(text); } catch { data = []; }

        $('view-count').textContent = data.length;

        if (!data || data.length === 0) {
            tbody.innerHTML = '<tr><td colspan="4" class="empty-row">No items scanned yet</td></tr>';
            return;
        }

        let html = '';
        data.forEach((row, i) => {
            const code = row.ITEM_CODE || row.item_code || row[0] || '';
            const name = row.ITEM_NAME || row.item_name || row[1] || '';
            const qty  = row.QTY       || row.qty       || row[2] || '';
            html += `<tr>
                <td>${i + 1}</td>
                <td>${escHtml(code)}</td>
                <td>${escHtml(name)}</td>
                <td>${escHtml(qty)}</td>
            </tr>`;
        });
        tbody.innerHTML = html;

    } catch (err) {
        console.error('View error:', err);
        tbody.innerHTML = '<tr><td colspan="4" class="empty-row">Connection error</td></tr>';
    }
}

function closeView() {
    showScreen('screen-main');
    $('input-itemcode').focus();
}

function escHtml(str) {
    const div = document.createElement('div');
    div.textContent = str;
    return div.innerHTML;
}

// ── BACK ───────────────────────────────────────────
function doBack() {
    // Reset state and go back to login
    state.username = '';
    state.zone = '';
    state.currentItem = null;
    $('login-username').value = '';
    $('login-zone').value = '';
    doClear();
    showScreen('screen-login');
}
