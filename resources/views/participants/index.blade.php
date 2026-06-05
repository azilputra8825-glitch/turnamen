@extends('layouts.tournament')
@section('title', 'Daftar Peserta')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2 page-header">
    <h4 class="fw-bold mb-0"><i class="bi bi-people-fill me-2"></i>Manajemen Peserta</h4>
    @if(auth()->user()->isAdmin())
    <a href="{{ route('participants.create') }}" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-circle me-1"></i> Tambah Peserta
    </a>
    @endif
</div>
<div class="row mb-3">
    <div class="col-md-4 ms-auto">
        <div class="input-group shadow-sm rounded">
            <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
            <input type="text" id="search-participants" class="form-control border-start-0 ps-0" placeholder="Cari nama, username, game ID..." autocomplete="off">
        </div>
    </div>
</div>

<div class="card" id="participants-card">
    @include('participants.partials.table')
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('search-participants');
    const container = document.getElementById('participants-card');

    let debounceTimer;

    function fetchParticipants(url, search = '') {
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
            console.error('Error fetching participants:', err);
            container.style.opacity = '1';
        });
    }

    // Debounced Search Input
    searchInput.addEventListener('input', function () {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
            const currentUrl = window.location.origin + window.location.pathname;
            fetchParticipants(currentUrl, this.value.trim());
        }, 300);
    });

    // Intercept Pagination Clicks inside the Container
    container.addEventListener('click', function (e) {
        const pageLink = e.target.closest('.pagination a');
        if (pageLink) {
            e.preventDefault();
            const url = pageLink.getAttribute('href');
            fetchParticipants(url, searchInput.value.trim());
            
            // Scroll to table card top smoothly
            container.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    });
});
</script>
@endpush
@endsection