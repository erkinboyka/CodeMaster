<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\GeminiService;

class GeminiServiceTest extends TestCase
{
    public function test_key_pool_throws_on_empty_keys(): void
    {
        config(['services.gemini.keys' => '']);
        $service = new GeminiService();

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('No Gemini API keys configured.');
        $service->keyPool();
    }

    public function test_key_pool_rotates_keys(): void
    {
        config(['services.gemini.keys' => 'key1,key2,key3']);
        $service = new GeminiService();

        $this->assertEquals('key1', $service->keyPool());
        $this->assertEquals('key2', $service->keyPool());
        $this->assertEquals('key3', $service->keyPool());
        $this->assertEquals('key1', $service->keyPool()); // wraps around
    }

    public function test_key_pool_single_key(): void
    {
        config(['services.gemini.keys' => 'only-one-key']);
        $service = new GeminiService();

        $this->assertEquals('only-one-key', $service->keyPool());
        $this->assertEquals('only-one-key', $service->keyPool());
    }

    public function test_build_contents_structure(): void
    {
        config(['services.gemini.keys' => 'test-key']);
        $service = new GeminiService();

        $contents = $service->buildContents(1, 'Hello');

        $this->assertIsArray($contents);
        $this->assertGreaterThan(0, count($contents));

        // First message should be system instruction from user role
        $this->assertEquals('user', $contents[0]['role']);
        $this->assertStringContainsString('CodeMaster', $contents[0]['parts'][0]['text']);

        // Second message should be model response
        $this->assertEquals('model', $contents[1]['role']);

        // Last message should be the user's actual message
        $last = end($contents);
        $this->assertEquals('user', $last['role']);
        $this->assertEquals('Hello', $last['parts'][0]['text']);
    }

    public function test_build_contents_includes_context(): void
    {
        config(['services.gemini.keys' => 'test-key']);
        $service = new GeminiService();

        $contents = $service->buildContents(1, 'Help', 'PHP arrays');

        // System instruction should contain context
        $systemText = $contents[0]['parts'][0]['text'];
        $this->assertStringContainsString('PHP arrays', $systemText);
    }

    public function test_build_contents_with_image(): void
    {
        config(['services.gemini.keys' => 'test-key']);
        $service = new GeminiService();

        $contents = $service->buildContentsWithImage(1, 'Look at this', base64_encode('fake-image'), 'image/png');

        $last = end($contents);
        $this->assertEquals('user', $last['role']);
        $this->assertCount(2, $last['parts']);
        $this->assertArrayHasKey('inline_data', $last['parts'][1]);
        $this->assertEquals('image/png', $last['parts'][1]['inline_data']['mime_type']);
    }

    public function test_trim_user_chat_messages_exists(): void
    {
        config(['services.gemini.keys' => 'test-key']);
        $service = new GeminiService();

        $this->assertTrue(method_exists($service, 'trimUserChatMessages'));
    }

    public function test_call_api_returns_fallback_on_failure(): void
    {
        config(['services.gemini.keys' => 'invalid-key']);
        $service = new GeminiService();

        // With an invalid key and no mock, it will fail and return fallback
        $result = $service->callApi([
            ['role' => 'user', 'parts' => [['text' => 'test']]],
        ]);

        $this->assertArrayHasKey('candidates', $result);
        $this->assertStringContainsString('temporarily unavailable', $result['candidates'][0]['content']['parts'][0]['text']);
    }
}
