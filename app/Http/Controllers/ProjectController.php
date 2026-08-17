<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ProjectController extends Controller
{
    public function publicIndex(Request $request)
    {
        $categories = Project::where('is_published', true)->distinct()->orderBy('category')->pluck('category');
        $projects = Project::query()->where('is_published', true)
            ->when($request->filled('category'), fn ($query) => $query->where('category', $request->string('category')))
            ->orderByDesc('is_featured')->orderBy('sort_order')->latest()->paginate(6)->withQueryString();

        return view('projects.index', compact('projects', 'categories'));
    }

    public function publicShow(Project $project)
    {
        abort_unless($project->is_published, 404);
        $related = Project::where('is_published', true)->whereKeyNot($project->id)
            ->where('category', $project->category)->limit(3)->get();

        return view('projects.show', compact('project', 'related'));
    }

    public function index(Request $request)
    {
        $projects = Project::query()
            ->when($request->filled('q'), fn ($query) => $query->where(fn ($inner) => $inner
                ->where('title', 'like', '%'.$request->q.'%')->orWhere('client', 'like', '%'.$request->q.'%')))
            ->when($request->filled('category'), fn ($query) => $query->where('category', $request->category))
            ->orderBy('sort_order')->latest()->paginate(15)->withQueryString();
        $categories = Project::distinct()->orderBy('category')->pluck('category');
        $modalProject = null;
        $modalRequest = $request->query('modal');
        if (is_string($modalRequest) && str_starts_with($modalRequest, 'edit-')) {
            $modalProject = Project::find(Str::after($modalRequest, 'edit-'));
        }

        return view('admin.projects.index', compact('projects', 'categories', 'modalProject'));
    }

    public function create()
    {
        return redirect()->route('admin.projects.index', ['modal' => 'create']);
    }

    public function store(Request $request)
    {
        $project = new Project;
        $this->persist($request, $project);

        return redirect()->route('admin.projects.index')->with('success', 'Proyek berhasil ditambahkan.');
    }

    public function edit(Project $project)
    {
        return redirect()->route('admin.projects.index', ['modal' => 'edit-'.$project->id]);
    }

    public function update(Request $request, Project $project)
    {
        $this->persist($request, $project);

        return redirect()->route('admin.projects.index')->with('success', 'Proyek berhasil diperbarui.');
    }

    public function destroy(Project $project)
    {
        $this->deleteManagedFiles(array_merge([$project->cover_image], $project->images ?? []));
        $project->delete();

        return back()->with('success', 'Proyek berhasil dihapus.');
    }

    private function persist(Request $request, Project $project): void
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:150'],
            'slug' => ['nullable', 'string', 'max:170', Rule::unique('projects', 'slug')->ignore($project->id)],
            'client' => ['nullable', 'string', 'max:150'],
            'category' => ['required', 'string', 'max:100'],
            'summary' => ['required', 'string', 'max:500'],
            'description' => ['nullable', 'string'],
            'technologies' => ['nullable', 'string', 'max:1000'],
            'project_url' => ['nullable', 'url', 'max:500'],
            'repository_url' => ['nullable', 'url', 'max:500'],
            'year' => ['nullable', 'integer', 'min:2015', 'max:'.(now()->year + 2)],
            'status' => ['required', Rule::in(['completed', 'ongoing', 'concept'])],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'cover_image' => ['nullable', 'image', 'max:5120'],
            'gallery_images' => ['nullable', 'array', 'max:15'],
            'gallery_images.*' => ['image', 'max:5120'],
            'existing_images' => ['nullable', 'array'],
            'existing_images.*' => ['string', 'max:500'],
        ]);

        $slug = Str::slug(($validated['slug'] ?? null) ?: $validated['title']);
        if ($project->slug !== $slug && Project::where('slug', $slug)->whereKeyNot($project->id)->exists()) {
            $slug .= '-'.Str::lower(Str::random(5));
        }

        $existingImages = array_values(array_intersect($project->images ?? [], $validated['existing_images'] ?? []));
        $removedImages = array_diff($project->images ?? [], $existingImages);
        $this->deleteManagedFiles($removedImages);

        $cover = $project->cover_image;
        if ($request->hasFile('cover_image')) {
            $this->deleteManagedFiles([$cover]);
            $cover = '/storage/'.$request->file('cover_image')->store('projects/covers', 'public');
        }

        $newImages = [];
        foreach ($request->file('gallery_images', []) as $image) {
            $newImages[] = '/storage/'.$image->store('projects/gallery', 'public');
        }

        $project->fill(collect($validated)->except([
            'cover_image', 'gallery_images', 'existing_images', 'technologies', 'slug', 'is_featured', 'is_published',
        ])->all());
        $project->slug = $slug;
        $project->cover_image = $cover ?: ($existingImages[0] ?? $newImages[0] ?? null);
        $project->images = array_values(array_unique(array_merge($existingImages, $newImages)));
        $project->technologies = collect(explode(',', $validated['technologies'] ?? ''))->map(fn ($item) => trim($item))->filter()->unique()->values()->all();
        $project->is_featured = $request->boolean('is_featured');
        $project->is_published = $request->boolean('is_published');
        $project->sort_order = $validated['sort_order'] ?? 0;
        $project->save();
    }

    private function deleteManagedFiles(array $paths): void
    {
        foreach (array_filter($paths) as $path) {
            if (str_starts_with($path, '/storage/')) {
                Storage::disk('public')->delete(Str::after($path, '/storage/'));
            }
        }
    }
}
