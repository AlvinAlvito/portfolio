<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class PublicExperienceTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_portfolio_pages_are_rendered(): void
    {
        $project = Project::create([
            'title' => 'Test Product', 'slug' => 'test-product', 'category' => 'Web Application',
            'summary' => 'Produk digital untuk pengujian.', 'technologies' => ['Laravel', 'React'],
            'cover_image' => '/assets/portfolio/projects/1.png', 'images' => ['/assets/portfolio/projects/1.png'],
            'is_published' => true, 'is_featured' => true,
        ]);

        $this->get(route('home'))->assertOk()
            ->assertSee('Web & aplikasi,', false)
            ->assertSee('/assets/js/preferences.js', false)
            ->assertSee("|| 'dark'", false)
            ->assertSee('languageSwitch', false)
            ->assertSee('themeSwitch', false)
            ->assertSee('Digital footprint')
            ->assertSee('/assets/portfolio/showcase/github.png', false)
            ->assertSee('s01.flagcounter.com/count2/APal', false)
            ->assertSee('Avinto Project')
            ->assertSee('Test Product');
        $this->get(route('projects.index'))->assertOk()->assertSee('Web Application')->assertSee('Test Product');
        $this->get(route('projects.show', $project))->assertOk()->assertSee('Laravel')->assertSee('Produk digital untuk pengujian.');
        $this->get(route('profile'))->assertOk()->assertSee('Paris Alvito')->assertSee('Supervisor Divisi IT')->assertSee('journey-card', false);
        $this->get(route('games'))->assertOk()->assertSee('Neon Runner')->assertSee('Violet Blocks')->assertSee('Pixel Memory')->assertSee('/assets/js/games.js', false);
        $this->get(route('seo.robots'))->assertOk()->assertSee('/sitemap.xml');
        $this->get(route('seo.sitemap'))->assertOk()->assertHeader('Content-Type', 'application/xml; charset=UTF-8')->assertSee(route('home'), false);
    }

    public function test_portfolio_archive_uses_six_projects_per_page_and_custom_pagination(): void
    {
        foreach (range(1, 8) as $number) {
            Project::create([
                'title' => 'Project '.$number, 'slug' => 'project-'.$number,
                'category' => 'Web Application', 'summary' => 'Ringkasan project '.$number,
                'is_published' => true, 'sort_order' => $number,
            ]);
        }

        $this->get(route('projects.index'))->assertOk()
            ->assertSee('Project 6')->assertDontSee('Project 7')
            ->assertSee('Menampilkan <strong>1-6</strong> dari <strong>8</strong> proyek', false);
        $this->get(route('projects.index', ['page' => 2]))->assertOk()
            ->assertSee('Project 7')->assertSee('Project 8')
            ->assertSee('Menampilkan <strong>7-8</strong> dari <strong>8</strong> proyek', false);
    }

    public function test_database_schema_contains_no_legacy_assessment_tables(): void
    {
        $this->assertTrue(Schema::hasTable('projects'));
        $this->assertTrue(Schema::hasTable('orders'));
        $this->assertFalse(Schema::hasTable('questions'));
        $this->assertFalse(Schema::hasTable('responses'));
        $this->assertFalse(Schema::hasTable('response_answers'));
    }

    public function test_order_form_stores_a_client_inquiry(): void
    {
        $payload = [
            'name' => 'Calon Klien', 'phone' => '081234567890', 'email' => 'client@example.com',
            'organization' => 'Example Studio', 'project_type' => 'Website Perusahaan',
            'details' => 'Kami membutuhkan website perusahaan dengan katalog layanan dan dashboard admin.',
            'budget_range' => 'Rp3 - 7 juta', 'desired_deadline' => now()->addMonth()->toDateString(),
            'contact_preference' => 'whatsapp',
        ];

        $this->post(route('orders.store'), $payload)->assertRedirect(route('orders.success'));
        $this->assertDatabaseHas('orders', ['name' => 'Calon Klien', 'status' => 'new']);
    }

    public function test_chatbot_can_answer_from_local_portfolio_context_without_api(): void
    {
        config(['services.groq.key' => null]);

        Project::create([
            'title' => 'Fallback Portfolio', 'slug' => 'fallback-portfolio',
            'category' => 'Web Application', 'summary' => 'Produk digital untuk chatbot.',
            'is_published' => true, 'is_featured' => true,
        ]);

        $this->postJson(route('chatbot.respond'), ['message' => 'Berapa biaya bikin website?'])
            ->assertOk()
            ->assertJsonPath('reply', fn ($reply) => str_contains($reply, 'Rp500 ribu'));

        $this->postJson(route('chatbot.respond'), ['message' => 'Siapa Alvin dari CV?'])
            ->assertOk()
            ->assertJsonPath('reply', fn ($reply) => str_contains($reply, 'Supervisor Divisi IT'));
    }

    public function test_admin_pages_require_session_and_can_manage_inquiry_status(): void
    {
        $this->get(route('admin.index'))->assertRedirect(route('home'));
        $order = Order::create([
            'name' => 'Client', 'phone' => '08123', 'project_type' => 'Aplikasi Mobile',
            'details' => 'Aplikasi pemesanan dengan kebutuhan integrasi API dan notifikasi.',
            'budget_range' => 'Rp7 - 15 juta', 'contact_preference' => 'whatsapp',
        ]);

        $this->withSession(['is_admin' => true])->get(route('admin.orders.show', $order))->assertOk()->assertSee('Aplikasi Mobile');
        $this->withSession(['is_admin' => true])->put(route('admin.orders.update', $order), ['status' => 'contacted', 'admin_notes' => 'Sudah dihubungi'])->assertRedirect();
        $this->assertDatabaseHas('orders', ['id' => $order->id, 'status' => 'contacted']);
        $this->withSession(['is_admin' => true])->get(route('admin.index'))->assertOk()->assertSee('Business Overview');
    }

    public function test_admin_can_create_and_update_project_with_uploaded_media(): void
    {
        Storage::fake('public');
        $response = $this->withSession(['is_admin' => true])->post(route('admin.projects.store'), [
            'title' => 'Client Platform', 'category' => 'Sistem Informasi',
            'summary' => 'Platform operasional untuk kebutuhan calon klien.',
            'description' => 'Deskripsi lengkap proyek.', 'technologies' => 'Laravel, React, MySQL',
            'status' => 'completed', 'year' => 2026, 'sort_order' => 1,
            'is_featured' => '1', 'is_published' => '1',
            'cover_image' => UploadedFile::fake()->image('cover.jpg', 1200, 800),
            'gallery_images' => [UploadedFile::fake()->image('screen.jpg', 1200, 800)],
        ]);

        $response->assertRedirect(route('admin.projects.index'));
        $project = Project::where('slug', 'client-platform')->firstOrFail();
        $this->assertSame(['Laravel', 'React', 'MySQL'], $project->technologies);
        Storage::disk('public')->assertExists(str_replace('/storage/', '', $project->cover_image));

        $this->withSession(['is_admin' => true])->put(route('admin.projects.update', $project), [
            'title' => 'Client Platform Updated', 'slug' => $project->slug,
            'category' => $project->category, 'summary' => $project->summary,
            'description' => $project->description, 'technologies' => 'Laravel, Go',
            'status' => 'ongoing', 'sort_order' => 2, 'is_published' => '1',
            'existing_images' => $project->images,
        ])->assertRedirect(route('admin.projects.index'));

        $this->assertDatabaseHas('projects', ['id' => $project->id, 'title' => 'Client Platform Updated', 'status' => 'ongoing']);
    }

    public function test_admin_project_editor_uses_modals_and_custom_pagination(): void
    {
        foreach (range(1, 18) as $number) {
            Project::create([
                'title' => 'Admin Project '.$number, 'slug' => 'admin-project-'.$number,
                'category' => 'Web Application', 'summary' => 'Ringkasan admin project '.$number,
                'is_published' => true, 'sort_order' => $number,
            ]);
        }

        $project = Project::firstOrFail();

        $this->withSession(['is_admin' => true])->get(route('admin.projects.index'))->assertOk()
            ->assertSee('projectCreateModal', false)
            ->assertSee('projectEditModal'.$project->id, false)
            ->assertSee('admin-pagination', false)
            ->assertSee('<strong>1-15</strong>', false)
            ->assertSee('<strong>18</strong> data', false);

        $this->withSession(['is_admin' => true])->get(route('admin.projects.create'))
            ->assertRedirect(route('admin.projects.index', ['modal' => 'create']));

        $this->withSession(['is_admin' => true])->get(route('admin.projects.edit', $project))
            ->assertRedirect(route('admin.projects.index', ['modal' => 'edit-'.$project->id]));
    }
}
