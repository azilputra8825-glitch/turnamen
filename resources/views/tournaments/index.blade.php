@extends('layouts.tournament')
@section('title', 'Daftar Turnamen')
@section('content')

<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2 page-header">
    <h4 class="fw-bold mb-0"><i class="bi bi-trophy me-2"></i>Manajemen Turnamen</h4>
    @if(auth()->user()->isAdmin())
    <a href="{{ route('tournaments.create') }}" class="btn btn-success btn-sm">
        <i class="bi bi-plus-circle me-1"></i> Buat Turnamen
    </a>
    @endif
</div>

<div class="row mb-3">
    <div class="col-md-4 ms-auto">
        <div class="input-group shadow-sm rounded">
            <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
            <input type="text" id="search-tournaments" class="form-control border-start-0 ps-0" placeholder="Cari nama turnamen atau game..." autocomplete="off">
        </div>
    </div>
</div>

<div id="tournaments-grid-container">
    @include('tournaments.partials.grid')
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('search-tournaments');
    const container = document.getElementById('tournaments-grid-container');

    let debounceTimer;

    function fetchTournaments(url, search = '') {
        const fetchUrl = new URL(url);
        if (search) {
            fetchUrl.searchParams.set('search', search);
        } else {
            fetchUrl.searchParams.delete('search');
        }

        // Show subtle loading state
        container.style.opacity = '0.5';

        fetch(fetchUrl, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (!response.ok) throw new Error('Network response error');
            return response.text();
        })
        .then(html => {
            container.innerHTML = html;
            container.style.opacity = '1';
        })
        .catch(err => {
            console.error('Error fetching tournaments:', err);
            container.style.opacity = '1';
        });
    }

    // Debounced Search Input
    searchInput.addEventListener('input', function () {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
            const currentUrl = window.location.origin + window.location.pathname;
            fetchTournaments(currentUrl, this.value.trim());
        }, 300);
    });

    // Intercept Pagination Clicks inside the Container
    container.addEventListener('click', function (e) {
        const pageLink = e.target.closest('.pagination a');
        if (pageLink) {
            e.preventDefault();
            const url = pageLink.getAttribute('href');
            fetchTournaments(url, searchInput.value.trim());
            
            // Scroll to grid top smoothly
            container.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    });
});
</script>
@endpush
@endsection