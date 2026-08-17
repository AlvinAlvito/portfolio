<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ChatbotTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_layout_contains_vinto_chatbot(): void
    {
        $this->get(route('home'))->assertOk()->assertSee('id="chatInput"', false)->assertSee('Vinto AI')->assertSee('/assets/js/chatbot.js', false);
    }

    public function test_chatbot_returns_groq_response_without_exposing_key(): void
    {
        config(['services.groq.key' => 'secret-test-key', 'services.groq.model' => 'llama-test-model']);
        Http::fake(['api.groq.com/*' => Http::response(['choices' => [['message' => ['content' => 'Kami dapat membantu membangun website Anda.']]]])]);

        $this->postJson(route('chatbot.respond'), ['message' => 'Bisa buat website perusahaan?', 'history' => []])
            ->assertOk()->assertJson(['reply' => 'Kami dapat membantu membangun website Anda.']);

        Http::assertSent(fn ($request) => $request->hasHeader('Authorization', 'Bearer secret-test-key')
            && ! str_contains($request->url(), 'secret-test-key')
            && str_contains($request['messages'][0]['content'], 'Avinto Project'));
    }

    public function test_chatbot_rejects_invalid_or_oversized_input(): void
    {
        $this->postJson(route('chatbot.respond'), ['message' => ''])->assertUnprocessable()->assertJsonValidationErrors('message');
        $this->postJson(route('chatbot.respond'), ['message' => str_repeat('a', 1201)])->assertUnprocessable()->assertJsonValidationErrors('message');
    }
}
