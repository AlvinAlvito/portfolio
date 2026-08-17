@php
    $isEditing = $project->exists;
    $formContext = $isEditing ? 'edit-'.$project->id : 'create';
    $categoryListId = $isEditing ? 'categoryOptions'.$project->id : 'categoryOptionsCreate';
@endphp

<form method="POST" enctype="multipart/form-data" action="{{ $isEditing ? route('admin.projects.update',$project) : route('admin.projects.store') }}" class="project-editor project-editor-modal">
    @csrf
    @if($isEditing)@method('PUT')@endif
    <input type="hidden" name="form_context" value="{{ $formContext }}">
    <div class="editor-main">
        <section class="data-card editor-section">
            <div class="data-card-head">
                <div>
                    <h3>Informasi utama</h3>
                    <small>Judul, konteks, dan cerita proyek</small>
                </div>
            </div>
            <div class="form-stack editor-body">
                <div class="form-grid">
                    <div class="field">
                        <label>Judul proyek *</label>
                        <input class="input" name="title" value="{{ old('title',$project->title) }}" required>
                    </div>
                    <div class="field">
                        <label>Slug URL</label>
                        <input class="input" name="slug" value="{{ old('slug',$project->slug) }}" placeholder="Otomatis dari judul">
                    </div>
                </div>
                <div class="form-grid">
                    <div class="field">
                        <label>Klien / pemilik</label>
                        <input class="input" name="client" value="{{ old('client',$project->client) }}">
                    </div>
                    <div class="field">
                        <label>Kategori *</label>
                        <input class="input" name="category" value="{{ old('category',$project->category ?: 'Web Development') }}" required list="{{ $categoryListId }}">
                        <datalist id="{{ $categoryListId }}">
                            @foreach(['Sistem Informasi','Website Institusi','Mobile & Web','Web Application','UI/UX Design'] as $categoryOption)
                                <option>{{ $categoryOption }}</option>
                            @endforeach
                        </datalist>
                    </div>
                </div>
                <div class="field">
                    <label>Ringkasan *</label>
                    <textarea class="input textarea" name="summary" rows="3" maxlength="500" required>{{ old('summary',$project->summary) }}</textarea>
                </div>
                <div class="field">
                    <label>Deskripsi lengkap</label>
                    <textarea class="input textarea" name="description" rows="8">{{ old('description',$project->description) }}</textarea>
                </div>
            </div>
        </section>

        <section class="data-card editor-section">
            <div class="data-card-head">
                <div>
                    <h3>Media proyek</h3>
                    <small>Gunakan JPG, PNG, atau WebP maksimal 5MB per gambar</small>
                </div>
            </div>
            <div class="form-stack editor-body">
                <div class="field">
                    <label>{{ $project->cover_image ? 'Ganti cover' : 'Cover baru' }}</label>
                    <input class="input file-input" type="file" name="cover_image" accept="image/*">
                    @if($project->cover_image)
                        <div class="current-cover">
                            <img src="{{ $project->cover_image }}" alt="Cover saat ini">
                            <small>Cover saat ini</small>
                        </div>
                    @endif
                </div>
                <div class="field">
                    <label>Tambah screenshot galeri</label>
                    <input class="input file-input" type="file" name="gallery_images[]" accept="image/*" multiple>
                    <small>Maksimal 15 gambar per unggahan.</small>
                </div>
                @if($project->images)
                    <div class="field">
                        <label>Galeri saat ini</label>
                        <div class="existing-gallery">
                            @foreach($project->images as $image)
                                <label>
                                    <img src="{{ $image }}" alt="">
                                    <span><input type="checkbox" name="existing_images[]" value="{{ $image }}" checked> Pertahankan</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </section>
    </div>

    <aside class="editor-sidebar">
        <section class="data-card editor-section">
            <div class="data-card-head"><h3>Pengaturan</h3></div>
            <div class="form-stack editor-body">
                <div class="field">
                    <label>Teknologi</label>
                    <input class="input" name="technologies" value="{{ old('technologies',implode(', ',$project->technologies ?? [])) }}" placeholder="Laravel, React, MySQL">
                    <small>Pisahkan dengan koma.</small>
                </div>
                <div class="field">
                    <label>URL website</label>
                    <input class="input" type="url" name="project_url" value="{{ old('project_url',$project->project_url) }}">
                </div>
                <div class="field">
                    <label>URL repository</label>
                    <input class="input" type="url" name="repository_url" value="{{ old('repository_url',$project->repository_url) }}">
                </div>
                <div class="form-grid">
                    <div class="field">
                        <label>Tahun</label>
                        <input class="input" type="number" name="year" min="2015" max="{{ now()->year+2 }}" value="{{ old('year',$project->year) }}">
                    </div>
                    <div class="field">
                        <label>Urutan</label>
                        <input class="input" type="number" name="sort_order" min="0" value="{{ old('sort_order',$project->sort_order ?? 0) }}">
                    </div>
                </div>
                <div class="field">
                    <label>Status proyek</label>
                    <select class="select" name="status">
                        @foreach(['completed'=>'Selesai','ongoing'=>'Sedang dikembangkan','concept'=>'Konsep'] as $value=>$label)
                            <option value="{{ $value }}" @selected(old('status',$project->status ?: 'completed')===$value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <label class="toggle-row">
                    <input type="checkbox" name="is_featured" value="1" @checked(old('is_featured',$project->is_featured))>
                    <span><strong>Proyek unggulan</strong><small>Prioritaskan di beranda.</small></span>
                </label>
                <label class="toggle-row">
                    <input type="checkbox" name="is_published" value="1" @checked(old('is_published',$project->exists ? $project->is_published : true))>
                    <span><strong>Publikasikan</strong><small>Tampilkan pada website.</small></span>
                </label>
                <button class="btn btn-primary" type="submit"><i data-lucide="save"></i> Simpan proyek</button>
            </div>
        </section>
    </aside>
</form>
