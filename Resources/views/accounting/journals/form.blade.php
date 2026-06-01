@extends('nexcore_client_manager::layouts.accounting')

@section('title', (isset($journal) ? 'Edit' : 'New') . ' Journal - ' . $client->company_name)
@section('page_heading', isset($journal) ? 'EDIT JOURNAL' : 'NEW JOURNAL')

@section('content')
<div class="sl-animate d1">
    <div class="sl-page-header">
        <div style="display:flex; align-items:center; gap:12px;">
            <div style="width:40px; height:40px; border-radius:10px; background:linear-gradient(135deg, rgba(245,158,11,0.15), rgba(245,158,11,0.05)); border:1px solid rgba(245,158,11,0.3); display:flex; align-items:center; justify-content:center;">
                <i class="fas fa-book" style="color:#f59e0b; font-size:16px;"></i>
            </div>
            <div>
                <h1 class="sl-page-title" style="margin:0;">{{ isset($journal) ? 'Edit Journal' : 'New Journal Entry' }}</h1>
                <span class="sl-page-subtitle">{{ $client->company_name }}</span>
            </div>
        </div>
        <div style="margin-left:auto;">
            <a href="{{ route('nexcore.clients.show.accounting.journals', $client->id) }}" class="neon-btn neon-btn-ghost"><i class="fas fa-arrow-left"></i> Back to Journals</a>
        </div>
    </div>
</div>

@if($errors->any())
<div class="sl-verdict reject sl-mb-md sl-animate d2" style="padding:14px 20px;">
    <div class="sl-verdict-icon" style="width:32px;height:32px;font-size:16px;"><i class="fas fa-exclamation-triangle"></i></div>
    <div>
        <div class="sl-verdict-text" style="font-size:15px;">Please correct the following errors:</div>
        <ul style="margin:6px 0 0; padding-left:20px; font-size:13px; color:var(--text-secondary);">
            @foreach($errors->all() as $error) <li>{{ $error }}</li> @endforeach
        </ul>
    </div>
</div>
@endif

<form method="POST" id="journalForm"
      action="{{ isset($journal) ? route('nexcore.clients.show.accounting.journals.update', [$client->id, $journal->id]) : route('nexcore.clients.show.accounting.journals.store', $client->id) }}">
    @csrf
    @if(isset($journal)) @method('PUT') @endif

    @php
        $stmtRefBadge = null;
        if (isset($journal) && $journal->source === 'bank_import' && $journal->reference) {
            try {
                $stmtRefBadge = \Modules\NexcoreClientManager\Models\NexcoreBankStatement::where('batch_ref', $journal->reference)->value('statement_ref');
            } catch (\Exception $e) {
                $stmtRefBadge = null;
            }
        }
    @endphp

    {{-- Header --}}
    <div class="sl-card sl-animate d2">
        <div class="sl-card-header">
            <div class="sl-card-title" style="color:#f59e0b;"><i class="fas fa-info-circle"></i> Journal Details</div>
        </div>
        <div style="padding:24px;">
            <div style="display:grid; grid-template-columns:160px 1fr 160px; gap:20px;">
                <div class="sl-field">
                    <label>Journal No.</label>
                    <input type="text" name="journal_number" value="{{ old('journal_number', $journal->journal_number ?? $journalNumber ?? '') }}" readonly style="font-family:var(--font-mono); color:#f59e0b; font-weight:600; background:var(--bg-surface);">
                </div>
                <div class="sl-field">
                    <label>Description <span style="color:var(--accent-red);">*</span></label>
                    <input type="text" name="description" value="{{ old('description', $journal->description ?? '') }}" required placeholder="Description of this journal entry">
                </div>
                <div class="sl-field">
                    <label>Date <span style="color:var(--accent-red);">*</span></label>
                    <input type="date" name="journal_date" value="{{ old('journal_date', isset($journal) ? $journal->journal_date->format('Y-m-d') : date('Y-m-d')) }}" required>
                </div>
            </div>
            <div style="display:grid; grid-template-columns:1fr 1fr 160px 160px; gap:20px; margin-top:20px;">
                <div class="sl-field">
                    <label>Reference</label>
                    <input type="text" name="reference" value="{{ old('reference', $journal->reference ?? '') }}" placeholder="Invoice no., receipt no., etc.">
                </div>
                <div class="sl-field">
                    <label>Statement Ref</label>
                    @if($stmtRefBadge)
                    <div style="font-family:var(--font-mono); font-size:13px; font-weight:700; color:#06b6d4; background:rgba(6,182,212,0.1); border:1px solid rgba(6,182,212,0.3); border-radius:var(--radius-sm); padding:10px 14px;">{{ $stmtRefBadge }}</div>
                    @else
                    <div style="font-size:13px; font-style:italic; color:var(--text-muted); background:var(--bg-raised); border:1px solid var(--border-default); border-radius:var(--radius-sm); padding:10px 14px;">No data available</div>
                    @endif
                </div>
                <div class="sl-field">
                    <label>Source <span style="color:var(--accent-red);">*</span></label>
                    <select name="source" required style="background:var(--bg-raised); color:var(--text-primary); border:1px solid var(--border-default); border-radius:var(--radius-sm); padding:10px 14px; font-size:15px; font-family:var(--font-body);">
                        @foreach($sources as $key => $label)
                            <option value="{{ $key }}" {{ old('source', $journal->source ?? 'manual') == $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="sl-field">
                    <label>Status <span style="color:var(--accent-red);">*</span></label>
                    <select name="status" required style="background:var(--bg-raised); color:var(--text-primary); border:1px solid var(--border-default); border-radius:var(--radius-sm); padding:10px 14px; font-size:15px; font-family:var(--font-body);">
                        @foreach($statuses as $key => $label)
                            <option value="{{ $key }}" {{ old('status', $journal->status ?? 'draft') == $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>

    {{-- Journal Lines --}}
    <div class="sl-card sl-animate d3">
        <div class="sl-card-header" style="display:flex; justify-content:space-between; align-items:center;">
            <div class="sl-card-title" style="color:#f59e0b;"><i class="fas fa-list-ol"></i> Journal Lines</div>
            <button type="button" onclick="addLine()" class="neon-btn neon-btn-green" style="padding:6px 14px; font-size:12px;"><i class="fas fa-plus"></i> Add Line</button>
        </div>
        <div style="padding:0;">
            <table class="sl-table" id="linesTable" style="margin:0;">
                <thead>
                    <tr>
                        <th style="width:40px;">#</th>
                        <th style="min-width:250px;">Account <span style="color:var(--accent-red);">*</span></th>
                        <th>Description</th>
                        <th style="width:160px; text-align:right;">Debit (R)</th>
                        <th style="width:160px; text-align:right;">Credit (R)</th>
                        <th style="width:50px;"></th>
                    </tr>
                </thead>
                <tbody id="linesBody">
                </tbody>
                <tfoot>
                    <tr style="background:rgba(245,158,11,0.06);">
                        <td colspan="3" style="text-align:right; font-weight:700; color:#f59e0b; font-size:14px; padding-right:20px;">TOTALS</td>
                        <td style="font-family:var(--font-mono); font-weight:700; color:var(--accent-green); text-align:right;" id="totalDebit">R 0.00</td>
                        <td style="font-family:var(--font-mono); font-weight:700; color:var(--accent-red); text-align:right;" id="totalCredit">R 0.00</td>
                        <td></td>
                    </tr>
                    <tr id="balanceRow" style="display:none;">
                        <td colspan="3" style="text-align:right; font-weight:700; color:var(--accent-red); font-size:13px;">OUT OF BALANCE</td>
                        <td colspan="2" style="font-family:var(--font-mono); font-weight:700; color:var(--accent-red); font-size:13px;" id="balanceDiff"></td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    {{-- Actions --}}
    <div class="sl-animate d4" style="display:flex; justify-content:flex-end; gap:12px; margin-top:20px;">
        <a href="{{ route('nexcore.clients.show.accounting.journals', $client->id) }}" class="neon-btn neon-btn-ghost"><i class="fas fa-times"></i> Cancel</a>
        <button type="submit" class="neon-btn neon-btn-amber neon-pulse"><i class="fas fa-save"></i> {{ isset($journal) ? 'Update Journal' : 'Save Journal' }}</button>
    </div>
</form>
@endsection

@push('scripts')
<style>
.acct-search-wrap { position:relative; width:100%; }
.acct-search-input {
    width:100%; background:var(--bg-raised); color:var(--text-primary);
    border:1px solid var(--border-default); border-radius:var(--radius-sm);
    padding:8px 30px 8px 10px; font-size:13px; font-family:var(--font-body);
    box-sizing:border-box;
}
.acct-search-input:focus { border-color:#f59e0b; outline:none; box-shadow:0 0 0 2px rgba(245,158,11,0.15); }
.acct-search-input.has-value { color:#f59e0b; font-weight:600; font-family:var(--font-mono); font-size:12px; }
.acct-search-clear {
    position:absolute; right:6px; top:50%; transform:translateY(-50%);
    background:none; border:none; color:var(--text-muted); cursor:pointer;
    font-size:13px; padding:2px; display:none; line-height:1;
}
.acct-search-clear:hover { color:var(--accent-red); }
.acct-search-wrap.has-value .acct-search-clear { display:block; }
.acct-dropdown {
    display:none; position:absolute; top:100%; left:0; right:0; z-index:999;
    max-height:240px; overflow-y:auto; background:var(--bg-raised);
    border:1px solid rgba(245,158,11,0.3); border-top:none;
    border-radius:0 0 var(--radius-sm) var(--radius-sm);
    box-shadow:0 8px 24px rgba(0,0,0,0.4);
}
.acct-dropdown.open { display:block; }
.acct-option {
    padding:8px 10px; cursor:pointer; font-size:12px; display:flex;
    align-items:center; gap:8px; transition:background 0.1s;
    border-bottom:1px solid var(--border-subtle);
}
.acct-option:last-child { border-bottom:none; }
.acct-option:hover, .acct-option.highlighted { background:rgba(245,158,11,0.1); }
.acct-option-code { font-family:var(--font-mono); color:#f59e0b; font-weight:700; font-size:11px; white-space:nowrap; }
.acct-option-name { color:var(--text-secondary); font-size:12px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
.acct-option-empty { padding:12px 10px; color:var(--text-muted); font-size:12px; text-align:center; font-style:italic; }
.acct-dropdown mark { background:rgba(245,158,11,0.3); color:#f59e0b; border-radius:2px; padding:0 1px; }
</style>
<script>
var accounts = @json($accounts->map(fn($a) => ['id' => $a->id, 'code' => $a->account_code, 'name' => $a->account_name]));
var lineCount = 0;
var activeDropdown = null;

function buildAccountPicker(name, selectedId) {
    var wrap = document.createElement('div');
    wrap.className = 'acct-search-wrap';

    var hidden = document.createElement('input');
    hidden.type = 'hidden';
    hidden.name = name;
    hidden.required = true;

    var search = document.createElement('input');
    search.type = 'text';
    search.className = 'acct-search-input';
    search.placeholder = 'Type to search accounts...';
    search.autocomplete = 'off';

    var clearBtn = document.createElement('button');
    clearBtn.type = 'button';
    clearBtn.className = 'acct-search-clear';
    clearBtn.innerHTML = '<i class="fas fa-times"></i>';

    var dropdown = document.createElement('div');
    dropdown.className = 'acct-dropdown';

    wrap.appendChild(hidden);
    wrap.appendChild(search);
    wrap.appendChild(clearBtn);
    wrap.appendChild(dropdown);

    if (selectedId) {
        var match = accounts.find(function(a) { return a.id == selectedId; });
        if (match) {
            hidden.value = match.id;
            search.value = match.code + ' - ' + match.name;
            search.classList.add('has-value');
            wrap.classList.add('has-value');
        }
    }

    var hlIdx = -1;

    function renderDropdown(query) {
        dropdown.innerHTML = '';
        hlIdx = -1;
        var q = (query || '').toLowerCase().trim();
        var filtered = accounts.filter(function(a) {
            if (!q) return true;
            return a.code.toLowerCase().indexOf(q) !== -1 || a.name.toLowerCase().indexOf(q) !== -1;
        });

        if (filtered.length === 0) {
            var empty = document.createElement('div');
            empty.className = 'acct-option-empty';
            empty.textContent = 'No accounts found';
            dropdown.appendChild(empty);
            return;
        }

        filtered.forEach(function(a, i) {
            var opt = document.createElement('div');
            opt.className = 'acct-option';
            opt.dataset.id = a.id;
            opt.dataset.code = a.code;
            opt.dataset.name = a.name;
            opt.dataset.idx = i;

            var codeSpan = document.createElement('span');
            codeSpan.className = 'acct-option-code';
            codeSpan.innerHTML = q ? highlightMatch(a.code, q) : a.code;

            var nameSpan = document.createElement('span');
            nameSpan.className = 'acct-option-name';
            nameSpan.innerHTML = q ? highlightMatch(a.name, q) : a.name;

            opt.appendChild(codeSpan);
            opt.appendChild(nameSpan);

            opt.addEventListener('mousedown', function(e) {
                e.preventDefault();
                selectAccount(hidden, search, wrap, dropdown, a);
            });

            dropdown.appendChild(opt);
        });
    }

    function highlightMatch(text, q) {
        var idx = text.toLowerCase().indexOf(q);
        if (idx === -1) return escapeHtml(text);
        return escapeHtml(text.substring(0, idx)) + '<mark>' + escapeHtml(text.substring(idx, idx + q.length)) + '</mark>' + escapeHtml(text.substring(idx + q.length));
    }

    function escapeHtml(t) {
        var d = document.createElement('span');
        d.textContent = t;
        return d.innerHTML;
    }

    search.addEventListener('focus', function() {
        if (activeDropdown && activeDropdown !== dropdown) {
            activeDropdown.classList.remove('open');
        }
        activeDropdown = dropdown;
        if (search.classList.contains('has-value')) {
            search.select();
        }
        renderDropdown(search.classList.contains('has-value') ? '' : search.value);
        dropdown.classList.add('open');
    });

    search.addEventListener('input', function() {
        search.classList.remove('has-value');
        wrap.classList.remove('has-value');
        hidden.value = '';
        renderDropdown(search.value);
        dropdown.classList.add('open');
    });

    search.addEventListener('blur', function() {
        setTimeout(function() {
            dropdown.classList.remove('open');
            if (!hidden.value && search.value) {
                search.value = '';
            }
        }, 200);
    });

    search.addEventListener('keydown', function(e) {
        var opts = dropdown.querySelectorAll('.acct-option');
        if (e.key === 'ArrowDown') {
            e.preventDefault();
            hlIdx = Math.min(hlIdx + 1, opts.length - 1);
            updateHighlight(opts);
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            hlIdx = Math.max(hlIdx - 1, 0);
            updateHighlight(opts);
        } else if (e.key === 'Enter') {
            e.preventDefault();
            if (hlIdx >= 0 && opts[hlIdx]) {
                var o = opts[hlIdx];
                var a = { id: o.dataset.id, code: o.dataset.code, name: o.dataset.name };
                selectAccount(hidden, search, wrap, dropdown, a);
            }
        } else if (e.key === 'Escape') {
            dropdown.classList.remove('open');
            search.blur();
        }
    });

    function updateHighlight(opts) {
        opts.forEach(function(o, i) {
            o.classList.toggle('highlighted', i === hlIdx);
        });
        if (opts[hlIdx]) opts[hlIdx].scrollIntoView({ block: 'nearest' });
    }

    clearBtn.addEventListener('click', function() {
        hidden.value = '';
        search.value = '';
        search.classList.remove('has-value');
        wrap.classList.remove('has-value');
        search.focus();
    });

    return wrap;
}

function selectAccount(hidden, search, wrap, dropdown, a) {
    hidden.value = a.id;
    search.value = a.code + ' - ' + a.name;
    search.classList.add('has-value');
    wrap.classList.add('has-value');
    dropdown.classList.remove('open');
}

function fmtAmt(val) {
    var num = parseFloat(String(val).replace(/\s/g, ''));
    if (isNaN(num) || num === 0) return '';
    return num.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ' ');
}

function rawAmt(str) {
    return String(str).replace(/\s/g, '');
}

function addLine(data) {
    lineCount++;
    var idx = lineCount - 1;
    var d = data || {account_id:'', description:'', debit_amount:'', credit_amount:''};
    var row = document.createElement('tr');
    row.className = 'jnl-line';
    row.innerHTML = '<td style="color:var(--text-muted);" class="line-num">'+lineCount+'</td>'
        +'<td class="acct-cell"></td>'
        +'<td><input type="text" name="lines['+idx+'][description]" value="'+(d.description||'')+'" placeholder="Line description" style="width:100%; background:var(--bg-raised); color:var(--text-primary); border:1px solid var(--border-default); border-radius:var(--radius-sm); padding:8px 10px; font-size:14px; font-family:var(--font-body);"></td>'
        +'<td><input type="text" inputmode="decimal" name="lines['+idx+'][debit_amount]" value="'+fmtAmt(d.debit_amount)+'" placeholder="0.00" class="calc-line" style="width:100%; background:var(--bg-raised); color:var(--accent-green); border:1px solid var(--border-default); border-radius:var(--radius-sm); padding:8px 10px; font-size:14px; font-family:var(--font-mono); font-weight:600; text-align:right;"></td>'
        +'<td><input type="text" inputmode="decimal" name="lines['+idx+'][credit_amount]" value="'+fmtAmt(d.credit_amount)+'" placeholder="0.00" class="calc-line" style="width:100%; background:var(--bg-raised); color:var(--accent-red); border:1px solid var(--border-default); border-radius:var(--radius-sm); padding:8px 10px; font-size:14px; font-family:var(--font-mono); font-weight:600; text-align:right;"></td>'
        +'<td><button type="button" onclick="removeLine(this)" style="background:none; border:none; color:var(--accent-red); cursor:pointer; font-size:15px;"><i class="fas fa-times-circle"></i></button></td>';

    var acctCell = row.querySelector('.acct-cell');
    acctCell.appendChild(buildAccountPicker('lines['+idx+'][account_id]', d.account_id));

    document.getElementById('linesBody').appendChild(row);
    row.querySelectorAll('.calc-line').forEach(function(el) {
        el.addEventListener('input', recalcTotals);
        el.addEventListener('focus', function() { this.value = rawAmt(this.value); });
        el.addEventListener('blur', function() { this.value = fmtAmt(this.value); recalcTotals(); });
    });
    recalcTotals();
}

function removeLine(btn) {
    if (document.querySelectorAll('.jnl-line').length <= 2) {
        alert('A journal must have at least 2 lines.');
        return;
    }
    btn.closest('tr').remove();
    renumberLines();
    recalcTotals();
}

function renumberLines() {
    document.querySelectorAll('.jnl-line').forEach(function(r, i) {
        r.querySelector('.line-num').textContent = i + 1;
    });
}

function recalcTotals() {
    var totalD = 0, totalC = 0;
    document.querySelectorAll('.jnl-line').forEach(function(r) {
        var d = parseFloat(rawAmt(r.querySelector('[name*="[debit_amount]"]').value)) || 0;
        var c = parseFloat(rawAmt(r.querySelector('[name*="[credit_amount]"]').value)) || 0;
        totalD += d;
        totalC += c;
    });
    document.getElementById('totalDebit').textContent = 'R ' + totalD.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ' ');
    document.getElementById('totalCredit').textContent = 'R ' + totalC.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ' ');
    var diff = Math.abs(totalD - totalC);
    var balRow = document.getElementById('balanceRow');
    if (diff > 0.001) {
        balRow.style.display = '';
        document.getElementById('balanceDiff').textContent = 'Difference: R ' + diff.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ' ');
    } else {
        balRow.style.display = 'none';
    }
}

document.addEventListener('click', function(e) {
    if (!e.target.closest('.acct-search-wrap') && activeDropdown) {
        activeDropdown.classList.remove('open');
    }
});

document.getElementById('journalForm').addEventListener('submit', function() {
    this.querySelectorAll('.calc-line').forEach(function(el) {
        el.value = rawAmt(el.value);
    });
});

// Init lines
@if(isset($journal) && $journal->lines->count())
    @foreach($journal->lines as $line)
        addLine({account_id:{{ $line->account_id }}, description:{!! json_encode($line->description ?? '') !!}, debit_amount:'{{ $line->debit_amount > 0 ? $line->debit_amount : '' }}', credit_amount:'{{ $line->credit_amount > 0 ? $line->credit_amount : '' }}'});
    @endforeach
@else
    addLine();
    addLine();
@endif
</script>
@endpush
