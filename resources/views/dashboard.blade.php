@extends('layouts.tournament')
@section('title', 'Dashboard')
@section('content')

<div class="d-flex justify-content-between align-items-center mb-4 mt-1 flex-wrap gap-2 page-header">
    <h4 class="fw-bold mb-0"><i class="bi bi-speedometer2 me-2 text-primary"></i>Dashboard</h4>
    <span class="text-muted small">Selamat datang, <strong>{{ auth()->user()->name }}</strong>!</span>
</div>

{{-- Statistik --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-sm-6 col-md-3">
        <div class="card stat-card border-start border-primary border-3 p-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small">Total Peserta</div>
                    <div class="fs-2 fw-bold text-primary">{{ $stats['total_participants'] }}</div>
                </div>
                <i class="bi bi-people-fill fs-1 text-primary opacity-25"></i>
            </div>
        </div>
    </div>
    <div class="col-6 col-sm-6 col-md-3">
        <div class="card stat-card border-start border-success border-3 p-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small">Total Turnamen</div>
                    <div class="fs-2 fw-bold text-success">{{ $stats['total_tournaments'] }}</div>
                </div>
                <i class="bi bi-trophy-fill fs-1 text-success opacity-25"></i>
            </div>
        </div>
    </div>
    <div class="col-6 col-sm-6 col-md-3">
        <div class="card stat-card border-start border-warning border-3 p-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small">Turnamen Aktif</div>
                    <div class="fs-2 fw-bold text-warning">{{ $stats['ongoing_tournaments'] }}</div>
                </div>
                <i class="bi bi-lightning-fill fs-1 text-warning opacity-25"></i>
            </div>
        </div>
    </div>
    <div class="col-6 col-sm-6 col-md-3">
        <div class="card stat-card border-start border-info border-3 p-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small">Total Match</div>
                    <div class="fs-2 fw-bold text-info">{{ $stats['total_matches'] }}</div>
                </div>
                <i class="bi bi-controller fs-1 text-info opacity-25"></i>
            </div>
        </div>
    </div>
</div>

@if(auth()->user()->isAdmin())
{{-- Grafik Analytics --}}
<div class="row g-3 mb-4">
    {{-- Tren Bulanan --}}
    <div class="col-12 col-lg-8">
        <div class="card p-4 h-100 shadow-sm border-0">
            <h6 class="fw-bold mb-3 text-dark"><i class="bi bi-graph-up me-2 text-primary"></i>Tren Pendaftaran & Pendapatan (6 Bulan Terakhir)</h6>
            <div style="position: relative; height: 310px;">
                <canvas id="monthlyTrendChart"></canvas>
            </div>
        </div>
    </div>
    
    {{-- Status Turnamen --}}
    <div class="col-12 col-md-6 col-lg-4">
        <div class="card p-4 h-100 shadow-sm border-0">
            <h6 class="fw-bold mb-3 text-dark"><i class="bi bi-pie-chart-fill me-2 text-success"></i>Status Turnamen</h6>
            <div style="position: relative; height: 310px;" class="d-flex align-items-center justify-content-center">
                <canvas id="tournamentStatusChart"></canvas>
            </div>
        </div>
    </div>
</div>
@endif

<div class="row g-3 mb-4">
    @if(auth()->user()->isAdmin())
    {{-- Game Terpopuler --}}
    <div class="col-12 col-md-6 col-lg-7">
        <div class="card p-4 h-100 shadow-sm border-0">
            <h6 class="fw-bold mb-3 text-dark"><i class="bi bi-controller me-2 text-warning"></i>Game Terpopuler</h6>
            <div style="position: relative; height: 220px;">
                @if(empty($gamePopularityData['labels']))
                    <div class="h-100 d-flex flex-column align-items-center justify-content-center text-muted">
                        <i class="bi bi-controller fs-2 mb-2"></i>
                        <span>Belum ada data turnamen</span>
                    </div>
                @else
                    <canvas id="gamePopularityChart"></canvas>
                @endif
            </div>
        </div>
    </div>
    @endif

    {{-- Quick Actions --}}
    <div class="col-12 col-md-6 {{ auth()->user()->isAdmin() ? 'col-lg-5' : 'col-lg-12' }}">
        <div class="card p-4 h-100 shadow-sm border-0">
            <h6 class="fw-bold mb-3 text-dark"><i class="bi bi-lightning-charge-fill me-2 text-danger"></i>Aksi Cepat</h6>
            <div class="d-flex gap-2 flex-wrap flex-grow-1 align-content-start">
                @if(auth()->user()->isAdmin())
                <a href="{{ route('participants.create') }}" class="btn btn-primary d-flex align-items-center justify-content-center gap-1 py-2 px-3 flex-fill">
                    <i class="bi bi-person-plus"></i> Tambah Peserta
                </a>
                <a href="{{ route('tournaments.create') }}" class="btn btn-success d-flex align-items-center justify-content-center gap-1 py-2 px-3 flex-fill">
                    <i class="bi bi-trophy"></i> Buat Turnamen
                </a>
                <a href="{{ route('matches.create') }}" class="btn btn-info text-white d-flex align-items-center justify-content-center gap-1 py-2 px-3 flex-fill">
                    <i class="bi bi-controller"></i> Jadwalkan Match
                </a>
                @endif
                <a href="{{ route('tournaments.index') }}" class="btn btn-outline-secondary d-flex align-items-center justify-content-center gap-1 py-2 px-3 flex-fill">
                    <i class="bi bi-list-ul"></i> Lihat Turnamen
                </a>
            </div>
        </div>
    </div>
</div>

@if(auth()->user()->isAdmin())
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // 1. Tournament Status Chart (Doughnut)
    const ctxStatus = document.getElementById('tournamentStatusChart').getContext('2d');
    new Chart(ctxStatus, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($tournamentStatusData['labels']) !!},
            datasets: [{
                data: {!! json_encode($tournamentStatusData['values']) !!},
                backgroundColor: ['#4f46e5', '#f59e0b', '#10b981'], // Indigo, Amber, Emerald
                borderWidth: 2,
                borderColor: '#ffffff',
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        boxWidth: 12,
                        padding: 15,
                        font: {
                            family: "'Segoe UI', system-ui, -apple-system, sans-serif",
                            size: 11
                        }
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.label || '';
                            if (label) {
                                label += ': ';
                            }
                            label += context.raw + ' Turnamen';
                            return label;
                        }
                    }
                }
            },
            cutout: '65%'
        }
    });

    // 2. Game Popularity Chart (Horizontal Bar)
    const elGame = document.getElementById('gamePopularityChart');
    if (elGame) {
        const ctxGame = elGame.getContext('2d');
        new Chart(ctxGame, {
            type: 'bar',
            data: {
                labels: {!! json_encode($gamePopularityData['labels'] ?? []) !!},
                datasets: [{
                    label: 'Jumlah Turnamen',
                    data: {!! json_encode($gamePopularityData['values'] ?? []) !!},
                    backgroundColor: 'rgba(233, 69, 96, 0.8)', // Accent
                    borderColor: '#e94560',
                    borderWidth: 1,
                    borderRadius: 6,
                    barThickness: 16
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0,
                            font: {
                                size: 11
                            }
                        },
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        }
                    },
                    y: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: {
                                size: 11,
                                weight: 'bold'
                            }
                        }
                    }
                }
            }
        });
    }

    // 3. Monthly Trend Chart (Line Chart with dual y-axes)
    const ctxTrend = document.getElementById('monthlyTrendChart').getContext('2d');
    
    const formatIDR = (value) => {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
        }).format(value);
    };

    new Chart(ctxTrend, {
        type: 'line',
        data: {
            labels: {!! json_encode($monthlyTrendData['labels']) !!},
            datasets: [
                {
                    label: 'Pendaftaran Peserta',
                    data: {!! json_encode($monthlyTrendData['registrations']) !!},
                    borderColor: '#4f46e5', // Indigo
                    backgroundColor: 'rgba(79, 70, 229, 0.05)',
                    borderWidth: 3,
                    tension: 0.35,
                    fill: true,
                    yAxisID: 'yRegistrations',
                    pointBackgroundColor: '#4f46e5',
                    pointHoverRadius: 6
                },
                {
                    label: 'Pendapatan (IDR)',
                    data: {!! json_encode($monthlyTrendData['revenue']) !!},
                    borderColor: '#10b981', // Emerald
                    backgroundColor: 'rgba(16, 185, 129, 0.02)',
                    borderWidth: 3,
                    tension: 0.35,
                    fill: true,
                    yAxisID: 'yRevenue',
                    pointBackgroundColor: '#10b981',
                    pointHoverRadius: 6
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                mode: 'index',
                intersect: false,
            },
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        boxWidth: 15,
                        padding: 10,
                        font: {
                            size: 11
                        }
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) {
                                label += ': ';
                            }
                            if (context.datasetIndex === 1) { // Revenue
                                label += formatIDR(context.raw);
                            } else {
                                label += context.raw + ' Pendaftaran';
                            }
                            return label;
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        font: {
                            size: 11
                        }
                    }
                },
                yRegistrations: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    title: {
                        display: true,
                        text: 'Pendaftaran',
                        font: {
                            size: 11,
                            weight: 'bold'
                        }
                    },
                    ticks: {
                        precision: 0,
                        font: {
                            size: 10
                        }
                    },
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    }
                },
                yRevenue: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    title: {
                        display: true,
                        text: 'Pendapatan (Rupiah)',
                        font: {
                            size: 11,
                            weight: 'bold'
                        }
                    },
                    ticks: {
                        callback: function(value) {
                            if (value >= 1000000) {
                                return 'Rp ' + (value / 1000000) + 'jt';
                            } else if (value >= 1000) {
                                return 'Rp ' + (value / 1000) + 'rb';
                            }
                            return 'Rp ' + value;
                        },
                        font: {
                            size: 10
                        }
                    },
                    grid: {
                        drawOnChartArea: false,
                    }
                }
            }
        }
    });
});
</script>
@endpush
@endif
@endsection