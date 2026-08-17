<article class="card project-card reveal">
    <a class="project-cover" href="{{ route('projects.show', $project) }}"><img src="{{ $project->cover_image ?: '/assets/img/generated/si-pental-hero.png' }}" alt="Tampilan proyek {{ $project->title }}" loading="lazy"><span class="project-arrow"><i data-lucide="arrow-up-right"></i></span></a>
    <div class="project-card-body"><div class="project-meta"><span>{{ $project->category }}</span><span>{{ $project->year ?: 'Project' }}</span></div><h3><a href="{{ route('projects.show', $project) }}">{{ $project->title }}</a></h3><p>{{ $project->summary }}</p><div class="tech-list">@foreach(array_slice($project->technologies ?? [], 0, 4) as $tech)<span>{{ $tech }}</span>@endforeach</div></div>
</article>
