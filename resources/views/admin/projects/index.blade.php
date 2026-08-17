@extends('layouts.main')
@section('title','Kelola Proyek')
@section('page-title','Portfolio Projects')
@section('content')
@php
    $createProject = new \App\Models\Project;
    $openModal = session('open_project_modal') ?: request('modal');
    if (old('form_context')) {
        $openModal = old('form_context');
    }
    $modalProjects = $projects->getCollection();
    if ($modalProject && ! $modalProjects->contains('id', $modalProject->id)) {
        $modalProjects = $modalProjects->push($modalProject);
    }
@endphp

<div class="admin-heading">
    <div>
        <h2>Kelola karya terbaik Anda</h2>
        <p>Atur detail, teknologi, tautan, cover, dan galeri screenshot proyek.</p>
    </div>
    <button class="btn btn-primary" type="button" data-open-modal="projectCreateModal">
        <i data-lucide="plus" size="17"></i> Tambah proyek
    </button>
</div>

@if(session('success'))<div class="flash-success">{{ session('success') }}</div>@endif
@if($errors->any())
    <div class="form-error admin-form-alert">
        <strong>Data belum dapat disimpan.</strong><br>{{ $errors->first() }}
    </div>
@endif

<form class="filter-panel" method="GET">
    <div class="filter-grid">
        <div class="field">
            <label>Pencarian</label>
            <input class="input" name="q" value="{{ request('q') }}" placeholder="Judul atau klien...">
        </div>
        <div class="field">
            <label>Kategori</label>
            <select class="select" name="category">
                <option value="">Semua kategori</option>
                @foreach($categories as $category)
                    <option @selected(request('category')===$category)>{{ $category }}</option>
                @endforeach
            </select>
        </div>
        <div class="filter-submit">
            <button class="btn btn-primary"><i data-lucide="search"></i> Cari</button>
            <a class="btn btn-soft btn-icon" href="{{ route('admin.projects.index') }}"><i data-lucide="rotate-ccw"></i></a>
        </div>
    </div>
</form>

<div class="data-card">
    <div class="data-card-head">
        <div>
            <h3>{{ $projects->total() }} proyek</h3>
            <small>Konten yang tampil pada halaman publik</small>
        </div>
    </div>
    <div class="table-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Proyek</th>
                    <th>Kategori</th>
                    <th>Teknologi</th>
                    <th>Urutan</th>
                    <th>Publikasi</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($projects as $project)
                    <tr>
                        <td>
                            <div class="project-table-cell">
                                <img src="{{ $project->cover_image }}" alt="">
                                <div>
                                    <strong>{{ $project->title }}</strong>
                                    <small>{{ $project->client ?: 'Tanpa klien' }} - {{ $project->year }}</small>
                                </div>
                            </div>
                        </td>
                        <td><span class="chart-tag">{{ $project->category }}</span></td>
                        <td>
                            <div class="tech-list compact">
                                @foreach(array_slice($project->technologies ?? [],0,3) as $tech)<span>{{ $tech }}</span>@endforeach
                            </div>
                        </td>
                        <td>#{{ $project->sort_order }}</td>
                        <td>
                            <span class="status-badge {{ $project->is_published ? 'status-stable' : 'status-monitor' }}">{{ $project->is_published ? 'Tayang' : 'Draft' }}</span>
                            @if($project->is_featured)<span class="featured-star" title="Unggulan"><i data-lucide="star"></i></span>@endif
                        </td>
                        <td>
                            <div class="table-actions">
                                <a class="btn btn-soft btn-sm btn-icon" href="{{ route('projects.show',$project) }}" target="_blank" title="Lihat halaman publik"><i data-lucide="eye"></i></a>
                                <button class="btn btn-soft btn-sm btn-icon" type="button" data-open-modal="projectEditModal{{ $project->id }}" title="Edit proyek"><i data-lucide="pencil"></i></button>
                                <form method="POST" action="{{ route('admin.projects.destroy',$project) }}" onsubmit="return confirm('Hapus proyek dan gambar upload terkait?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-danger-soft btn-sm btn-icon" title="Hapus proyek"><i data-lucide="trash-2"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">
                            <div class="empty-state"><i data-lucide="folder-x"></i><p>Belum ada proyek.</p></div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($projects->hasPages())
        <div class="pagination-shell admin-pagination-wrap">{{ $projects->onEachSide(1)->links('vendor.pagination.admin') }}</div>
    @endif
</div>

<div class="admin-modal project-modal {{ $openModal === 'create' ? 'open' : '' }}" id="projectCreateModal" role="dialog" aria-modal="true" aria-labelledby="projectCreateTitle">
    <div class="admin-modal-card project-modal-card">
        <div class="modal-head project-modal-head">
            <div>
                <span class="section-kicker">Project editor</span>
                <h2 id="projectCreateTitle">Tambah karya baru</h2>
                <p>Lengkapi informasi agar proyek tampil kuat di halaman publik.</p>
            </div>
            <button class="modal-close" type="button" data-close-modal><i data-lucide="x"></i></button>
        </div>
        @include('admin.projects._form', ['project' => $createProject])
    </div>
</div>

@foreach($modalProjects as $project)
    <div class="admin-modal project-modal {{ $openModal === 'edit-'.$project->id ? 'open' : '' }}" id="projectEditModal{{ $project->id }}" role="dialog" aria-modal="true" aria-labelledby="projectEditTitle{{ $project->id }}">
        <div class="admin-modal-card project-modal-card">
            <div class="modal-head project-modal-head">
                <div>
                    <span class="section-kicker">Project editor</span>
                    <h2 id="projectEditTitle{{ $project->id }}">Perbarui {{ $project->title }}</h2>
                    <p>Edit detail, media, status publikasi, dan urutan tampil.</p>
                </div>
                <button class="modal-close" type="button" data-close-modal><i data-lucide="x"></i></button>
            </div>
            @include('admin.projects._form', ['project' => $project])
        </div>
    </div>
@endforeach
@endsection
