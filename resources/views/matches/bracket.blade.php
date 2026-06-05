@extends('layouts.tournament')
@section('title', 'Bracket – ' . $tournament->name)

@section('content')

{{-- Header --}}
<div class="d-flex justify-content-between align-items-start mb-3 flex-wrap gap-2">
    <div>
        <a href="{{ route('tournaments.show', $tournament) }}" class="btn btn-outline-secondary btn-sm mb-2">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
        <h4 class="fw-bold mb-0">
            <i class="bi bi-diagram-3 me-2 text-warning"></i>Bracket Pertandingan
        </h4>
        <small class="text-muted">
            {{ $tournament->name }} &mdash; {{ $tournament->game_name }}
            <span class="badge bg-info text-dark ms-2">
                <i class="bi bi-people me-1"></i>{{ $tournament->participants->count() }} Peserta
            </span>
        </small>
    </div>
    <div class="d-flex gap-2 align-items-center flex-wrap">
        <button class="btn btn-warning btn-sm fw-bold" id="btnSaveWinner">
            <i class="bi bi-trophy-fill me-1"></i>Simpan Juara
        </button>
    </div>
</div>

{{-- Info bar --}}
<div class="alert alert-info alert-dismissible fade show py-2 small mb-2" role="alert">
    <i class="bi bi-info-circle-fill me-1"></i>
    <strong>Cara pakai:</strong> Klik kartu match untuk input skor & pilih pemenang. Skor langsung tersimpan ke database.
    Tekan <strong>Simpan Juara</strong> untuk menandai juara final.
    <button type="button" class="btn-close py-2" data-bs-dismiss="alert"></button>
</div>

{{-- Mobile scroll hint --}}
<div class="d-flex d-md-none align-items-center gap-2 mb-2 small text-muted">
    <i class="bi bi-arrows-move"></i>
    <span>Geser ke kanan untuk melihat bracket lengkap</span>
</div>

{{-- Bracket Canvas --}}
<div id="bracketOuter" style="
    position:relative;background:#0d1117;
    border:1px solid #21262d;border-radius:14px;
    overflow:auto;min-height:420px;width:100%;
    padding-bottom:24px;
">
    <svg id="cSvg" style="position:absolute;top:0;left:0;pointer-events:none;z-index:1;overflow:visible;"></svg>
    <div id="bracketArea" style="position:relative;min-width:960px;min-height:420px;z-index:2;"></div>
</div>

{{-- Legend --}}
<div class="d-flex gap-3 flex-wrap mt-2 small text-muted">
    <span><span class="badge" style="background:#2ea043">■</span> Selesai</span>
    <span><span class="badge" style="background:#1f6feb">■</span> Terjadwal</span>
    <span><span class="badge bg-secondary">■</span> Menunggu</span>
</div>

{{-- Score Input Modal --}}
<div class="modal fade" id="scoreModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width:380px">
        <div class="modal-content" style="background:#161b22;border:1px solid #30363d;color:#e6edf3;">
            <div class="modal-header" style="border-color:#30363d;background:#21262d;padding:.7rem 1rem;">
                <h6 class="modal-title fw-bold mb-0">
                    <i class="bi bi-pencil-square text-info me-2"></i>
                    Input Hasil Match
                </h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="scoreMatchLabel" class="text-center small text-muted mb-3"></div>

                {{-- Score Row --}}
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="flex-1 text-center" style="flex:1">
                        <div id="scoreP1Name" class="fw-semibold small mb-1 text-truncate"></div>
                        <input type="number" id="scoreP1" min="0" value="0"
                               class="form-control form-control-lg text-center fw-bold"
                               style="background:#21262d;border-color:#30363d;color:#e6edf3;font-size:1.5rem;">
                    </div>
                    <div class="text-muted fw-bold" style="font-size:1.2rem;">vs</div>
                    <div class="flex-1 text-center" style="flex:1">
                        <div id="scoreP2Name" class="fw-semibold small mb-1 text-truncate"></div>
                        <input type="number" id="scoreP2" min="0" value="0"
                               class="form-control form-control-lg text-center fw-bold"
                               style="background:#21262d;border-color:#30363d;color:#e6edf3;font-size:1.5rem;">
                    </div>
                </div>

                {{-- Winner Selection --}}
                <div class="mb-3">
                    <label class="form-label small text-muted">Pemenang:</label>
                    <div id="winnerBtns" class="d-grid gap-2"></div>
                </div>

                <div id="scoreError" class="alert alert-danger py-2 small d-none"></div>
            </div>
            <div class="modal-footer" style="border-color:#30363d;background:#21262d;">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-success btn-sm fw-bold" id="btnSaveScore">
                    <i class="bi bi-check-lg me-1"></i>Simpan Hasil
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Simple Winner Modal (tanpa skor — untuk Simpan Juara) --}}
<div class="modal fade" id="winnerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content" style="background:#161b22;border:1px solid #30363d;color:#e6edf3;">
            <div class="modal-header" style="border-color:#30363d;background:#21262d;padding:.6rem 1rem;">
                <h6 class="modal-title fw-bold mb-0">
                    <i class="bi bi-trophy-fill text-warning me-2"></i>Pilih Pemenang
                </h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted small mb-2">Siapa yang menang?</p>
                <div id="winnerOptions" class="d-grid gap-2"></div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<style>
@keyframes float {
    0%,100%{transform:translateY(0);}
    50%{transform:translateY(-6px);}
}
@keyframes champGlow {
    0%,100%{box-shadow:0 0 0 0 rgba(240,192,64,.5);}
    50%{box-shadow:0 0 0 8px rgba(240,192,64,0);}
}
#bracketOuter::-webkit-scrollbar{height:7px;width:7px;}
#bracketOuter::-webkit-scrollbar-track{background:#0d1117;}
#bracketOuter::-webkit-scrollbar-thumb{background:#30363d;border-radius:4px;}
.match-card .prow{transition:background .15s;}
.match-card .prow.clickable:hover{filter:brightness(1.25);}
</style>

<script>
// ── DATA ────────────────────────────────────────────────────────────
const RAW  = @json($matches);
const TID  = {{ $tournament->id }};
const CSRF = document.querySelector('meta[name="csrf-token"]').content;

// ── LAYOUT CONSTANTS ────────────────────────────────────────────────
const CW       = 188;   // card width
const ROW_H    = 34;    // row height per participant
const CH       = ROW_H * 2; // card height = 68
const BASE_GAP = 24;    // gap between adjacent cards in round 1
const UNIT_H   = CH + BASE_GAP; // = 92px, base vertical unit
const COL_GAP  = 80;    // horizontal gap (space for connector lines)
const COL_W    = CW + COL_GAP;
const PAD_L    = 28;
const PAD_T    = 52;    // top padding (for round labels)

// ── STATE ────────────────────────────────────────────────────────────
let matches = JSON.parse(JSON.stringify(RAW));
let winners = {};

// ── HELPERS ──────────────────────────────────────────────────────────
function byRound(arr) {
    const g = arr.reduce((g, m) => { (g[m.round] = g[m.round] || []).push(m); return g; }, {});
    // Sort each round's matches by match_number for correct positioning
    Object.values(g).forEach(ms => ms.sort((a, b) => a.match_number - b.match_number));
    return g;
}
function sortedRounds(g) {
    return Object.keys(g).map(Number).sort((a,b) => a-b);
}
function roundLabel(r, total) {
    if (r === total)     return '🏆 Final';
    if (r === total - 1) return 'Semi-Final';
    if (r === total - 2) return 'Perempat Final';
    return 'Babak ' + r;
}

// ── TREE-BASED POSITION ALGORITHM ────────────────────────────────────
// Each later-round match is centered between its two feeder matches.
// Uses match_number (not array index) for correctness with byes.
function calcPositions() {
    const g  = byRound(matches);
    const rs = sortedRounds(g);
    const pos = {};

    rs.forEach((r, ri) => {
        const x      = PAD_L + ri * COL_W;
        const rMatches = g[r];

        if (ri === 0) {
            // Round 1: sequential by match_number index
            rMatches.forEach((m, idx) => {
                pos[m.id] = { x, y: PAD_T + idx * UNIT_H };
            });
        } else {
            // Later rounds: center between feeder matches from previous round
            const prevR  = rs[ri - 1];
            const prevMs = g[prevR] || [];
            rMatches.forEach(m => {
                const mn = m.match_number;
                const f1 = prevMs.find(pm => pm.match_number === mn * 2 - 1);
                const f2 = prevMs.find(pm => pm.match_number === mn * 2);
                let y;
                if (f1 && f2 && pos[f1.id] != null && pos[f2.id] != null) {
                    y = (pos[f1.id].y + pos[f2.id].y) / 2;
                } else if (f1 && pos[f1.id] != null) {
                    y = pos[f1.id].y;
                } else if (f2 && pos[f2.id] != null) {
                    y = pos[f2.id].y;
                } else {
                    // Fallback
                    const idx = rMatches.indexOf(m);
                    y = PAD_T + idx * UNIT_H * Math.pow(2, ri);
                }
                pos[m.id] = { x, y };
            });
        }
    });
    return pos;
}

// ── DOM REFS ─────────────────────────────────────────────────────────
const svgEl  = document.getElementById('cSvg');
const areaEl = document.getElementById('bracketArea');

// ── SVG HELPERS ──────────────────────────────────────────────────────
function svgLine(x1, y1, x2, y2, color, dash) {
    const l = document.createElementNS('http://www.w3.org/2000/svg','line');
    l.setAttribute('x1',x1); l.setAttribute('y1',y1);
    l.setAttribute('x2',x2); l.setAttribute('y2',y2);
    l.setAttribute('stroke', color);
    l.setAttribute('stroke-width','2');
    l.setAttribute('stroke-linecap','round');
    if (dash) l.setAttribute('stroke-dasharray', dash);
    svgEl.appendChild(l);
}

// ── RENDER ────────────────────────────────────────────────────────────
function render() {
    areaEl.innerHTML = '';
    svgEl.innerHTML  = '';

    const positions = calcPositions();
    const g      = byRound(matches);
    const rs     = sortedRounds(g);
    const total  = Math.max(...rs);

    // Canvas size
    let maxX = 960, maxY = 420;
    matches.forEach(m => {
        const p = positions[m.id];
        if (p) {
            maxX = Math.max(maxX, p.x + CW + COL_GAP + CW + 40);
            maxY = Math.max(maxY, p.y + CH + 60);
        }
    });
    areaEl.style.minWidth  = maxX + 'px';
    areaEl.style.minHeight = maxY + 'px';
    svgEl.setAttribute('width', maxX);
    svgEl.setAttribute('height', maxY);

    // ── Round headers ────────────────────────────────────────────────
    rs.forEach((r, ri) => {
        const x = PAD_L + ri * COL_W;
        const el = document.createElement('div');
        el.style.cssText =
            `position:absolute;top:14px;left:${x}px;width:${CW}px;` +
            `text-align:center;font-size:11px;font-weight:700;letter-spacing:1.5px;` +
            `text-transform:uppercase;color:#58a6ff;font-family:monospace;`;
        el.textContent = roundLabel(r, total);
        areaEl.appendChild(el);
    });

    // Champion column header
    const champColX = PAD_L + rs.length * COL_W;
    const hdr = document.createElement('div');
    hdr.style.cssText =
        `position:absolute;top:14px;left:${champColX}px;width:${CW}px;` +
        `text-align:center;font-size:11px;font-weight:700;letter-spacing:1.5px;` +
        `text-transform:uppercase;color:#f0c040;font-family:monospace;`;
    hdr.textContent = '🏆 JUARA';
    areaEl.appendChild(hdr);

    // ── SVG connectors between rounds (match_number based) ───────────
    rs.forEach((r, ri) => {
        if (ri === rs.length - 1) return;
        const nextR  = rs[ri + 1];
        const curMs  = g[r];
        const nextMs = g[nextR];
        const midX   = PAD_L + ri * COL_W + CW + COL_GAP / 2;

        nextMs.forEach(nextM => {
            const pN = positions[nextM.id];
            if (!pN) return;

            const mn = nextM.match_number;
            const mA = curMs.find(m => m.match_number === mn * 2 - 1); // feeder slot 1
            const mB = curMs.find(m => m.match_number === mn * 2);     // feeder slot 2

            const wA = mA ? !!(winners[mA.id] ?? mA.winner_id) : false;
            const wB = mB ? !!(winners[mB.id] ?? mB.winner_id) : false;

            // Horizontal lines from each feeder card to midX
            if (mA) {
                const pA = positions[mA.id];
                if (pA) svgLine(pA.x + CW, pA.y + CH/2, midX, pA.y + CH/2, wA ? '#2ea043' : '#30363d');
            }
            if (mB) {
                const pB = positions[mB.id];
                if (pB) svgLine(pB.x + CW, pB.y + CH/2, midX, pB.y + CH/2, wB ? '#2ea043' : '#30363d');
            }

            // Vertical bracket line at midX
            if (mA && mB) {
                const pA = positions[mA.id], pB = positions[mB.id];
                if (pA && pB) {
                    const cVert = (wA && wB) ? '#2ea043' : '#30363d';
                    svgLine(midX, pA.y + CH/2, midX, pB.y + CH/2, cVert);
                }
            } else {
                // Only one feeder: draw short vertical to next match center
                const mS = mA || mB;
                if (mS) {
                    const pS = positions[mS.id];
                    if (pS) svgLine(midX, pS.y + CH/2, midX, pN.y + CH/2, '#30363d', '4,3');
                }
            }

            // Horizontal line from midX to next match card
            const cOut = (wA || wB) ? '#2ea043' : '#30363d';
            svgLine(midX, pN.y + CH/2, pN.x, pN.y + CH/2, cOut);
        });
    });

    // ── Find final match data ─────────────────────────────────────────
    const finalMs = g[total];
    // True final = last match by match_number in the final round
    const trueFinal = finalMs ? [...finalMs].sort((a,b) => (a.match_number||a.id) - (b.match_number||b.id)).pop() : null;
    // Vertical center of ALL final-round matches
    const finalCenterY = (() => {
        if (!finalMs || !finalMs.length) return PAD_T + CH / 2;
        const ys = finalMs.map(m => positions[m.id]).filter(Boolean).map(p => p.y + CH / 2);
        return ys.length ? (Math.min(...ys) + Math.max(...ys)) / 2 : PAD_T + CH / 2;
    })();
    // Find winner from any completed final-round match
    const finalWinner = (() => {
        if (!finalMs) return null;
        for (const m of finalMs) {
            const wid = winners[m.id] ?? m.winner_id;
            if (wid) {
                const wp = [m.participant1, m.participant2].find(p => p && p.id == wid);
                if (wp) return { wid, wp, match: m };
            }
        }
        return null;
    })();

    // Connector: Final → Champion
    if (finalMs && finalMs.length) {
        const firstP = positions[finalMs[0].id];
        const lastM  = finalMs[finalMs.length - 1];
        const lastP  = positions[lastM.id];
        const hasWin = !!finalWinner;
        const cLine  = hasWin ? '#f0c040' : '#30363d';
        const dash   = hasWin ? null : '6,3';
        if (firstP) {
            const rightX = firstP.x + CW;
            const midX   = rightX + COL_GAP / 2;
            if (finalMs.length === 1) {
                // Single final match: straight horizontal line
                svgLine(rightX, finalCenterY, champColX, finalCenterY, cLine, dash);
            } else {
                // Multiple matches: horizontal from each + vertical bracket + center to champion
                const topY    = firstP.y + CH / 2;
                const bottomY = lastP ? lastP.y + CH / 2 : topY;
                finalMs.forEach(m => {
                    const pM = positions[m.id];
                    if (pM) svgLine(pM.x + CW, pM.y + CH / 2, midX, pM.y + CH / 2, cLine, dash);
                });
                svgLine(midX, topY, midX, bottomY, cLine, dash);
                svgLine(midX, finalCenterY, champColX, finalCenterY, cLine, dash);
            }
        }
    }

    // ── Draw match cards ─────────────────────────────────────────────
    matches.forEach(m => drawCard(m, total, positions));

    // ── Champion box ─────────────────────────────────────────────────
    if (finalMs && finalMs.length) {
        const wp  = finalWinner ? finalWinner.wp : null;
        const cy  = finalCenterY - 44;

        const box = document.createElement('div');
        box.style.cssText =
            `position:absolute;left:${champColX}px;top:${cy}px;` +
            `width:${CW}px;text-align:center;z-index:10;`;

        if (wp) {
            box.innerHTML =
                `<div style="font-size:2rem;animation:float 2s ease-in-out infinite;">🏆</div>` +
                `<div style="margin-top:6px;padding:8px 12px;` +
                `background:linear-gradient(135deg,rgba(240,192,64,.18),rgba(240,192,64,.06));` +
                `border:1.5px solid #f0c040;border-radius:8px;` +
                `color:#f0c040;font-weight:700;font-size:.9rem;` +
                `text-shadow:0 0 10px rgba(240,192,64,.6);` +
                `white-space:nowrap;overflow:hidden;text-overflow:ellipsis;` +
                `animation:champGlow 2s ease-in-out infinite;">` +
                `${escHtml(wp.name)}</div>` +
                `<div style="color:#484f58;font-size:.6rem;letter-spacing:2px;margin-top:5px;">JUARA TURNAMEN</div>`;
        } else {
            box.innerHTML =
                `<div style="padding:14px 10px;border:1.5px dashed #30363d;border-radius:8px;` +
                `color:#484f58;font-size:.78rem;"><i>Menunggu pemenang final…</i></div>`;
        }
        areaEl.appendChild(box);
    }
}

// ── DRAW SINGLE CARD ─────────────────────────────────────────────────
function drawCard(m, total, positions) {
    const p = positions[m.id];
    if (!p) return;

    const wid = winners[m.id] ?? m.winner_id;
    const p1w = !!(wid && m.participant1 && wid == m.participant1.id);
    const p2w = !!(wid && m.participant2 && wid == m.participant2.id);

    let accent = '#484f58'; // TBD
    if (wid)                               accent = '#2ea043'; // done
    else if (m.participant1 && m.participant2) accent = '#1f6feb'; // active

    const wrap = document.createElement('div');
    wrap.dataset.matchId = m.id;
    wrap.style.cssText =
        `position:absolute;left:${p.x}px;top:${p.y}px;width:${CW}px;` +
        `user-select:none;z-index:10;`;

    const card = document.createElement('div');
    card.className = 'match-card';
    card.style.cssText =
        `border-radius:7px;overflow:hidden;` +
        `border:1px solid #21262d;border-left:3px solid ${accent};` +
        `box-shadow:0 3px 14px rgba(0,0,0,.5);background:#161b22;`;

    card.appendChild(makeRow(m.participant1, p1w, m));

    const divider = document.createElement('div');
    divider.style.cssText = 'height:1px;background:#21262d;';
    card.appendChild(divider);

    card.appendChild(makeRow(m.participant2, p2w, m));
    wrap.appendChild(card);

    // Match badge
    const badge = document.createElement('div');
    badge.style.cssText =
        `position:absolute;top:-9px;left:8px;` +
        `background:#0d1117;border:1px solid #21262d;` +
        `color:#484f58;font-size:9px;padding:1px 5px;` +
        `border-radius:3px;font-family:monospace;`;
    badge.textContent = 'M' + (m.match_number || m.id);
    wrap.appendChild(badge);

    areaEl.appendChild(wrap);
}

// ── PARTICIPANT ROW ───────────────────────────────────────────────────
function makeRow(participant, isWinner, match) {
    const canPick = !!(match.participant1 && match.participant2);
    const row = document.createElement('div');
    row.className = 'prow' + (canPick && participant ? ' clickable' : '');
    row.style.cssText =
        `display:flex;align-items:center;height:${ROW_H}px;` +
        `padding:0 8px 0 6px;gap:6px;` +
        `background:${isWinner ? 'rgba(46,160,67,.18)' : '#161b22'};` +
        `cursor:${canPick && participant ? 'pointer' : 'default'};`;

    if (canPick && participant) {
        row.addEventListener('mouseenter', () => {
            row.style.background = isWinner ? 'rgba(46,160,67,.3)' : 'rgba(255,255,255,.05)';
        });
        row.addEventListener('mouseleave', () => {
            row.style.background = isWinner ? 'rgba(46,160,67,.18)' : '#161b22';
        });
        row.addEventListener('click', e => { e.stopPropagation(); openScoreModal(match); });
    }

    // Seed badge
    const seed = document.createElement('span');
    seed.style.cssText =
        `min-width:18px;height:18px;border-radius:3px;flex-shrink:0;` +
        `background:${isWinner ? '#2ea043' : '#21262d'};` +
        `color:${isWinner ? '#fff' : '#484f58'};` +
        `font-size:9px;font-weight:700;font-family:monospace;` +
        `display:flex;align-items:center;justify-content:center;`;
    seed.textContent = participant ? ('#' + participant.id) : '?';
    row.appendChild(seed);

    // Name
    const name = document.createElement('span');
    name.style.cssText =
        `flex:1;font-size:12.5px;font-family:"Segoe UI",system-ui,sans-serif;` +
        `white-space:nowrap;overflow:hidden;text-overflow:ellipsis;` +
        `color:${isWinner ? '#3fb950' : (participant ? '#e6edf3' : '#484f58')};` +
        `font-weight:${isWinner ? '700' : '400'};`;
    if (participant) {
        name.textContent = participant.name;
    } else {
        name.innerHTML = '<i style="color:#484f58">TBD</i>';
    }
    row.appendChild(name);

    // Score (if completed)
    const score = match.winner_id || winners[match.id];
    if (score && participant) {
        const sc = document.createElement('span');
        sc.style.cssText =
            `font-size:11px;font-family:monospace;font-weight:700;` +
            `color:${isWinner ? '#3fb950' : '#484f58'};min-width:14px;text-align:right;`;
        const s1 = match.score_participant1 ?? 0;
        const s2 = match.score_participant2 ?? 0;
        const myScore = (match.participant1 && participant.id == match.participant1.id) ? s1 : s2;
        sc.textContent = myScore;
        row.appendChild(sc);
    }

    // Winner check icon
    if (isWinner) {
        const ico = document.createElement('span');
        ico.style.cssText = 'font-size:11px;';
        ico.textContent = '✓';
        ico.style.color = '#3fb950';
        row.appendChild(ico);
    }

    return row;
}

// ── SCORE MODAL ──────────────────────────────────────────────────────
let _scoreMatch = null;   // match currently being edited
let _selectedWinner = null;

function openScoreModal(match) {
    if (!match.participant1 || !match.participant2) {
        // TBD match — open simple winner picker instead
        openWinnerModal(match);
        return;
    }
    _scoreMatch      = match;
    _selectedWinner  = winners[match.id] ?? match.winner_id ?? null;

    document.getElementById('scoreMatchLabel').textContent =
        'Match #' + (match.match_number || match.id) + ' · Babak ' + match.round;
    document.getElementById('scoreP1Name').textContent = match.participant1.name;
    document.getElementById('scoreP2Name').textContent = match.participant2.name;
    document.getElementById('scoreP1').value = match.score_participant1 ?? 0;
    document.getElementById('scoreP2').value = match.score_participant2 ?? 0;
    document.getElementById('scoreError').classList.add('d-none');

    // Winner buttons
    const wb = document.getElementById('winnerBtns');
    wb.innerHTML = '';
    [match.participant1, match.participant2].forEach(p => {
        const btn = document.createElement('button');
        const isSel = _selectedWinner && _selectedWinner == p.id;
        btn.type = 'button';
        btn.className = 'btn btn-sm ' + (isSel ? 'btn-success fw-bold' : 'btn-outline-secondary');
        btn.innerHTML = (isSel ? '<i class="bi bi-check-circle-fill me-1"></i>' : '') + escHtml(p.name);
        btn.dataset.pid = p.id;
        btn.addEventListener('click', () => {
            _selectedWinner = p.id;
            wb.querySelectorAll('button').forEach(b => {
                const sel = b.dataset.pid == p.id;
                b.className = 'btn btn-sm ' + (sel ? 'btn-success fw-bold' : 'btn-outline-secondary');
                b.innerHTML = (sel ? '<i class="bi bi-check-circle-fill me-1"></i>' : '') + escHtml(
                    sel ? p.name : (b.dataset.pid == match.participant1.id ? match.participant1.name : match.participant2.name)
                );
            });
            // Auto-fill score winner logic: if scores are equal, don't auto-assign
            const s1 = parseInt(document.getElementById('scoreP1').value) || 0;
            const s2 = parseInt(document.getElementById('scoreP2').value) || 0;
        });
        wb.appendChild(btn);
    });

    new bootstrap.Modal(document.getElementById('scoreModal')).show();
}

// ── SAVE SCORE TO SERVER ─────────────────────────────────────────────
document.getElementById('btnSaveScore').addEventListener('click', async () => {
    const match = _scoreMatch;
    if (!match) return;

    const s1  = parseInt(document.getElementById('scoreP1').value) || 0;
    const s2  = parseInt(document.getElementById('scoreP2').value) || 0;
    const wid = _selectedWinner;
    const errEl = document.getElementById('scoreError');

    if (!wid) {
        errEl.textContent = '⚠️ Pilih pemenang terlebih dahulu!';
        errEl.classList.remove('d-none');
        return;
    }
    errEl.classList.add('d-none');

    const btn = document.getElementById('btnSaveScore');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Menyimpan…';

    try {
        const res  = await fetch('/matches/' + match.id + '/update-score', {
            method:  'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
            body:    JSON.stringify({ score_participant1: s1, score_participant2: s2, winner_id: wid }),
        });
        const data = await res.json();

        if (data.success) {
            // Gunakan data matches terbaru dari server (termasuk match baru babak berikutnya)
            if (data.matches) {
                matches = data.matches;
                // Sync winners dari server data
                matches.forEach(m => {
                    if (m.winner_id && !winners[m.id]) winners[m.id] = m.winner_id;
                });
            } else {
                // Fallback: update lokal
                const idx = matches.findIndex(m => m.id === match.id);
                if (idx !== -1) {
                    matches[idx].score_participant1 = s1;
                    matches[idx].score_participant2 = s2;
                    matches[idx].winner_id          = wid;
                    matches[idx].status             = 'completed';
                }
            }
            winners[match.id] = wid;
            saveLocal();
            bootstrap.Modal.getInstance(document.getElementById('scoreModal')).hide();
            render();
        } else {
            errEl.textContent = '❌ ' + (data.message ?? 'Terjadi kesalahan.');
            errEl.classList.remove('d-none');
        }
    } catch (_) {
        errEl.textContent = '❌ Gagal menyimpan. Periksa koneksi.';
        errEl.classList.remove('d-none');
    }

    btn.disabled = false;
    btn.innerHTML = '<i class="bi bi-check-lg me-1"></i>Simpan Hasil';
});

// ── WINNER MODAL (simple, untuk TBD) ─────────────────────────────────
function openWinnerModal(match) {
    const opts = document.getElementById('winnerOptions');
    opts.innerHTML = '';
    const curW = winners[match.id] ?? match.winner_id;

    [match.participant1, match.participant2].filter(Boolean).forEach(p => {
        const isSel = curW && curW == p.id;
        const btn = document.createElement('button');
        btn.className = isSel ? 'btn btn-success fw-bold' : 'btn btn-outline-secondary';
        btn.style.textAlign = 'left';
        btn.innerHTML = (isSel ? '✓ ' : '') + escHtml(p.name);
        btn.addEventListener('click', () => {
            winners[match.id] = p.id;
            saveLocal();
            bootstrap.Modal.getInstance(document.getElementById('winnerModal')).hide();
            render();
        });
        opts.appendChild(btn);
    });

    if (curW) {
        const clr = document.createElement('button');
        clr.className = 'btn btn-outline-danger btn-sm mt-1';
        clr.innerHTML = '<i class="bi bi-x-circle me-1"></i>Batalkan Pemenang';
        clr.addEventListener('click', () => {
            delete winners[match.id];
            saveLocal();
            bootstrap.Modal.getInstance(document.getElementById('winnerModal')).hide();
            render();
        });
        opts.appendChild(clr);
    }

    new bootstrap.Modal(document.getElementById('winnerModal')).show();
}

// ── LOCAL STORAGE ────────────────────────────────────────────────────
function saveLocal() {
    localStorage.setItem('bkt_' + TID, JSON.stringify({ winners }));
}

// ── SAVE WINNER BUTTON ────────────────────────────────────────────────
document.getElementById('btnSaveWinner').addEventListener('click', async () => {
    const g     = byRound(matches);
    const total = Math.max(...sortedRounds(g));
    const final = g[total]?.[0];
    const wid   = final ? (winners[final.id] ?? final.winner_id) : null;

    if (!wid || !final) {
        alert('⚠️ Tentukan pemenang Final terlebih dahulu!');
        return;
    }
    const wp = [final.participant1, final.participant2].find(p => p && p.id == wid);
    if (!confirm('Simpan "' + (wp?.name ?? '?') + '" sebagai Juara Turnamen?')) return;

    try {
        const res = await fetch('/tournaments/' + TID + '/bracket/save-winner', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
            body: JSON.stringify({
                match_id:           final.id,
                winner_id:          wid,
                score_participant1: final.score_participant1 ?? 0,
                score_participant2: final.score_participant2 ?? 0,
            }),
        });
        const data = await res.json();
        if (data.success) { alert('✅ Juara berhasil disimpan!'); location.reload(); }
        else              { alert('❌ Gagal: ' + (data.message ?? 'Terjadi kesalahan.')); }
    } catch (_) {
        alert('❌ Gagal menyimpan. Periksa koneksi jaringan.');
    }
});

// ── UTILITY ──────────────────────────────────────────────────────────
function escHtml(s) {
    return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
}

// ── INIT ─────────────────────────────────────────────────────────────
(function init() {
    const saved = localStorage.getItem('bkt_' + TID);
    if (saved) {
        try {
            const d = JSON.parse(saved);
            winners = d.winners ?? {};
        } catch (_) { winners = {}; }
    }
    // Seed winners from DB
    matches.forEach(m => {
        if (m.winner_id && !winners[m.id]) winners[m.id] = m.winner_id;
    });

    // Add keyframe animations to document
    const style = document.createElement('style');
    style.textContent =
        `@keyframes float{0%,100%{transform:translateY(0);}50%{transform:translateY(-6px);}}` +
        `@keyframes champGlow{0%,100%{box-shadow:0 0 0 0 rgba(240,192,64,.5);}50%{box-shadow:0 0 0 10px rgba(240,192,64,0);}}`;
    document.head.appendChild(style);

    render();
})();
</script>
@endpush
@endsection