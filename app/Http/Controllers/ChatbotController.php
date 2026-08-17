<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class ChatbotController extends Controller
{
    public function respond(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'message' => ['required', 'string', 'max:1200'],
            'history' => ['sometimes', 'array', 'max:10'],
            'history.*.role' => ['required_with:history', 'in:user,model'],
            'history.*.text' => ['required_with:history', 'string', 'max:1200'],
        ]);

        $apiKey = trim((string) config('services.groq.key'));
        $model = trim((string) config('services.groq.model', 'llama-3.3-70b-versatile'));

        if ($apiKey === '') {
            return response()->json([
                'reply' => $this->fallbackReply($validated['message']),
            ]);
        }

        if (! preg_match('#^[a-zA-Z0-9._/-]+$#', $model)) {
            Log::error('Nama model Groq tidak valid.');

            return response()->json(['reply' => $this->fallbackReply($validated['message'])]);
        }

        $messages = collect($validated['history'] ?? [])
            ->map(fn (array $item) => [
                'role' => $item['role'] === 'model' ? 'assistant' : 'user',
                'content' => $item['text'],
            ])
            ->push([
                'role' => 'user',
                'content' => $validated['message'],
            ])
            ->prepend([
                'role' => 'system',
                'content' => $this->systemInstruction(),
            ])
            ->values()
            ->all();

        try {
            $response = Http::acceptJson()
                ->withToken($apiKey)
                ->withOptions(['verify' => resource_path('certs/cacert.pem')])
                ->timeout(30)
                ->retry(2, 350, throw: false)
                ->post('https://api.groq.com/openai/v1/chat/completions', [
                    'model' => $model,
                    'messages' => $messages,
                    'temperature' => 0.65,
                    'max_completion_tokens' => 500,
                ]);

            if (! $response->successful()) {
                Log::warning('Groq chatbot request failed.', [
                    'status' => $response->status(),
                    'error' => $response->json('error.message'),
                ]);

                return response()->json(['reply' => $this->fallbackReply($validated['message'])]);
            }

            $reply = $response->json('choices.0.message.content');
            $reply = is_string($reply) && trim($reply) !== '' ? trim($reply) : null;

            if (! $reply) {
                return response()->json([
                    'message' => 'Respons tidak dapat ditampilkan. Coba ubah pertanyaanmu.',
                ], 502);
            }

            return response()->json(['reply' => $reply]);
        } catch (Throwable $exception) {
            Log::error('Groq chatbot connection error.', [
                'exception' => $exception->getMessage(),
            ]);

            return response()->json([
                'reply' => $this->fallbackReply($validated['message']),
            ]);
        }
    }

    private function systemInstruction(): string
    {
        $portfolio = config('portfolio');
        $projects = Project::where('is_published', true)->orderByDesc('is_featured')->limit(15)->get()
            ->map(fn (Project $project) => $project->title.' ('.implode(', ', $project->technologies ?? []).')')
            ->implode('; ');

        return <<<PROMPT
Kamu adalah "Vinto", asisten AI Avinto Project. Jawab dalam bahasa Indonesia yang ramah, profesional, singkat, dan membantu calon klien memahami layanan, kemampuan, pengalaman, proyek, serta cara memulai kerja sama dengan Paris Alvito (Alvin Alvito).

Informasi utama:
- Profil: {$portfolio['profile']}
- Identitas: Paris Alvito juga dikenal sebagai Alvin Alvito, pemilik Avinto Project.
- Pendidikan: S1 Ilmu Komputer UIN Sumatera Utara dan SMK Teknik Komputer & Jaringan.
- Pengalaman: Supervisor Divisi IT YP Pusat Olimpiade Sains Indonesia, Staff IT - Fullstack Developer POSI, Mitra Fullstack Developer PUSTIPADA UINSU, Freelance Fullstack Developer Avinto Project, Asisten Laboratorium Komputer UIN Sumatera Utara.
- Leadership: CTO CV Gokir - Gokir.id dan CTO GOMU.
- Layanan: Web Development, Mobile Development, UI/UX Design, IT Support dan Infrastructure.
- Teknologi utama: Go, Laravel, Express.js, React.js, Next.js, Remix.js, Flutter, PostgreSQL, MySQL, MongoDB, Docker, Cloudflare, dan Google Cloud.
- Portofolio terpublikasi: {$projects}
- Kontak WhatsApp: {$portfolio['contact']['phone']}
- Email: {$portfolio['contact']['email']}
- Lokasi: {$portfolio['contact']['location']}

Aturan:
- Arahkan calon klien yang serius ke formulir "Mulai Proyek" atau WhatsApp.
- Jangan menjanjikan harga, durasi, fitur, atau ketersediaan yang belum dikonfirmasi. Jelaskan bahwa estimasi final diberikan setelah diskusi kebutuhan.
- Bila ditanya harga awal, UI/UX mulai Rp100 ribu, mobile mulai Rp400 ribu, dan web mulai Rp500 ribu, tetapi tekankan bahwa biaya bergantung ruang lingkup.
- Jangan mengarang detail proyek, klien, testimoni, atau kemampuan yang tidak ada dalam informasi di atas.
- Untuk pertanyaan di luar Avinto Project, jawab singkat lalu arahkan kembali ke kebutuhan produk digital.
- Maksimal 3 paragraf pendek dan jangan mengaku sebagai manusia.
PROMPT;
    }

    private function fallbackReply(string $message): string
    {
        $portfolio = config('portfolio');
        $text = str($message)->lower()->toString();
        $hasIndonesian = preg_match('/\b(berapa|biaya|harga|bikin|buat|pesan|mulai|layanan|siapa|pengalaman|kontak|proyek|aplikasi|pendidikan|kuliah|sekolah|teknologi)\b/i', $message);
        $isEnglish = ! $hasIndonesian && preg_match('/\b(price|cost|order|service|website|app|application|who|experience|contact|portfolio|project|education|school|skill|stack|technology)\b/i', $message);
        $projects = Project::where('is_published', true)->orderByDesc('is_featured')->limit(5)->pluck('title')->implode(', ');

        $servicesId = 'Avinto Project melayani pembuatan website, sistem informasi/dashboard, aplikasi mobile Flutter, UI/UX design, API/backend, deployment, VPS, Cloudflare, Docker, dan IT support.';
        $servicesEn = 'Avinto Project builds websites, information systems, dashboards, Flutter mobile apps, UI/UX design, APIs/backends, deployment, VPS, Cloudflare, Docker, and IT support.';

        if (str_contains($text, 'harga') || str_contains($text, 'biaya') || str_contains($text, 'budget') || str_contains($text, 'price') || str_contains($text, 'cost')) {
            return $isEnglish
                ? 'Starting estimates: UI/UX from Rp100k, mobile apps from Rp400k, and websites from Rp500k. The final quote depends on scope, features, deadline, and integrations. Share your idea through the Start Project form or WhatsApp '.$portfolio['contact']['phone'].' so Alvin can estimate it properly.'
                : 'Estimasi awal: UI/UX mulai Rp100 ribu, aplikasi mobile mulai Rp400 ribu, dan website mulai Rp500 ribu. Harga final tetap menyesuaikan scope, fitur, deadline, dan integrasi. Ceritakan kebutuhan lewat form Mulai Proyek atau WhatsApp '.$portfolio['contact']['phone'].' agar bisa dihitung lebih tepat.';
        }

        if (str_contains($text, 'pesan') || str_contains($text, 'order') || str_contains($text, 'mulai') || str_contains($text, 'buat') || str_contains($text, 'bikin')) {
            return $isEnglish
                ? 'To start, send your name, WhatsApp number, project type, feature details, budget range, and expected deadline through the Start Project form. Alvin will review the scope, discuss the direction, then provide a realistic recommendation and estimate.'
                : 'Untuk mulai, isi form Mulai Proyek dengan nama, nomor WhatsApp, tipe proyek, detail fitur, range budget, dan target selesai. Setelah itu Alvin akan meninjau scope, berdiskusi singkat, lalu memberi rekomendasi solusi dan estimasi yang realistis.';
        }

        if (str_contains($text, 'siapa') || str_contains($text, 'profil') || str_contains($text, 'cv') || str_contains($text, 'pengalaman') || str_contains($text, 'who') || str_contains($text, 'experience')) {
            return $isEnglish
                ? 'Paris Alvito, also known as Alvin Alvito, is a Fullstack Developer, IT Supervisor, and CTO behind Avinto Project. His CV includes IT Supervisor at YP POSI, Fullstack Developer roles at POSI and PUSTIPADA UINSU, CTO roles at Gokir and GOMU, and 35+ completed web/app projects.'
                : 'Paris Alvito, juga dikenal sebagai Alvin Alvito, adalah Fullstack Developer, IT Supervisor, dan CTO di balik Avinto Project. Dari data CV, Alvin pernah menjadi Supervisor Divisi IT YP POSI, Fullstack Developer di POSI dan PUSTIPADA UINSU, CTO Gokir dan GOMU, serta menyelesaikan 35+ proyek web/aplikasi.';
        }

        if (str_contains($text, 'layanan') || str_contains($text, 'service') || str_contains($text, 'bisa apa') || str_contains($text, 'aplikasi') || str_contains($text, 'website')) {
            return $isEnglish
                ? $servicesEn.' If you already have an idea, describe the problem, required features, target users, and budget range so I can suggest the best first step.'
                : $servicesId.' Kalau sudah punya ide, jelaskan masalah, fitur yang dibutuhkan, target pengguna, dan range budget agar saya bisa bantu arahkan langkah awalnya.';
        }

        if (str_contains($text, 'pendidikan') || str_contains($text, 'kuliah') || str_contains($text, 'sekolah') || str_contains($text, 'education') || str_contains($text, 'school')) {
            return $isEnglish
                ? 'Based on Alvin’s CV, his education includes a Computer Science degree at UIN Sumatera Utara and Computer & Network Engineering at SMK Swasta Imelda Medan. That background supports his work across software development, infrastructure, and IT support.'
                : 'Berdasarkan CV Alvin, pendidikannya adalah S1 Ilmu Komputer di UIN Sumatera Utara serta Teknik Komputer & Jaringan di SMK Swasta Imelda Medan. Fondasi ini mendukung pekerjaan software development, infrastruktur, dan IT support.';
        }

        if (str_contains($text, 'skill') || str_contains($text, 'stack') || str_contains($text, 'teknologi') || str_contains($text, 'technology')) {
            return $isEnglish
                ? 'Alvin’s main stack includes Go, Laravel, Express.js, React.js, Next.js, Remix.js, Flutter, PostgreSQL, MySQL, MongoDB, Docker, Cloudflare, VPS, Google Cloud, Git, GitHub, and OpenAI Codex.'
                : 'Stack utama Alvin mencakup Go, Laravel, Express.js, React.js, Next.js, Remix.js, Flutter, PostgreSQL, MySQL, MongoDB, Docker, Cloudflare, VPS, Google Cloud, Git, GitHub, dan OpenAI Codex.';
        }

        if (str_contains($text, 'portofolio') || str_contains($text, 'portfolio') || str_contains($text, 'proyek') || str_contains($text, 'project')) {
            return $isEnglish
                ? 'Some published works include '.$projects.'. You can open the Portfolio page to see more screenshots, technologies, and project details.'
                : 'Beberapa karya terpublikasi antara lain '.$projects.'. Anda bisa membuka halaman Portofolio untuk melihat screenshot, teknologi, dan detail proyek lainnya.';
        }

        if (str_contains($text, 'kontak') || str_contains($text, 'whatsapp') || str_contains($text, 'email') || str_contains($text, 'contact')) {
            return $isEnglish
                ? 'You can contact Alvin via WhatsApp '.$portfolio['contact']['phone'].' or email '.$portfolio['contact']['email'].'. For project inquiries, the Start Project form is the cleanest way because it captures the scope and budget range.'
                : 'Anda bisa menghubungi Alvin lewat WhatsApp '.$portfolio['contact']['phone'].' atau email '.$portfolio['contact']['email'].'. Untuk permintaan proyek, form Mulai Proyek paling rapi karena langsung memuat scope dan range budget.';
        }

        return $isEnglish
            ? 'I can help you explore web, app, UI/UX, backend, deployment, and IT support needs with Avinto Project. Tell me what you want to build, your main features, target users, deadline, and budget range.'
            : 'Saya bisa bantu menjawab kebutuhan web, aplikasi, UI/UX, backend, deployment, dan IT support bersama Avinto Project. Ceritakan produk yang ingin dibuat, fitur utama, target pengguna, deadline, dan range budget Anda.';
    }
}
