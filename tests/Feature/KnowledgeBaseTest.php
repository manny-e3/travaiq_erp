<?php

namespace Tests\Feature;

use App\Models\KbArticle;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class KnowledgeBaseTest extends TestCase
{
    use RefreshDatabase;

    protected $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        // Setup Roles
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);

        // Setup Admin User
        $this->adminUser = User::factory()->create([
            'name' => 'System Admin',
            'email' => 'admin@admin.com',
            'is_active' => true,
        ]);
        $this->adminUser->assignRole($adminRole);
    }

    /** @test */
    public function public_can_view_published_articles_and_search()
    {
        // Create published and draft articles
        $published1 = KbArticle::create([
            'title' => 'How to reset router',
            'content' => 'Step 1: Press the power button.',
            'category' => 'Technical',
            'tags' => ['router', 'reset'],
            'is_published' => true,
            'views' => 10,
        ]);

        $published2 = KbArticle::create([
            'title' => 'Understanding your Invoice',
            'content' => 'Invoices are sent monthly.',
            'category' => 'Billing',
            'tags' => ['invoice', 'billing'],
            'is_published' => true,
            'views' => 20,
        ]);

        $draft = KbArticle::create([
            'title' => 'Internal Draft Notes',
            'content' => 'Private information.',
            'category' => 'General',
            'tags' => ['internal'],
            'is_published' => false,
        ]);

        // 1. Visit index page
        $response = $this->get(route('kb.index'));
        $response->assertStatus(200);
        $response->assertSee('How to reset router');
        $response->assertSee('Understanding your Invoice');
        $response->assertDontSee('Internal Draft Notes');

        // 2. Search query matches title or content
        $responseSearch = $this->get(route('kb.index', ['q' => 'router']));
        $responseSearch->assertStatus(200);
        $responseSearch->assertSee('How to reset router');
        $responseSearch->assertDontSee('Understanding your Invoice');

        // 3. Filter by category
        $responseCat = $this->get(route('kb.index', ['category' => 'Billing']));
        $responseCat->assertStatus(200);
        $responseCat->assertDontSee('How to reset router');
        $responseCat->assertSee('Understanding your Invoice');

        // 4. Filter by tag
        $responseTag = $this->get(route('kb.index', ['tag' => 'reset']));
        $responseTag->assertStatus(200);
        $responseTag->assertSee('How to reset router');
        $responseTag->assertDontSee('Understanding your Invoice');
    }

    /** @test */
    public function public_can_view_individual_article_and_increments_views()
    {
        $article = KbArticle::create([
            'title' => 'Setting Up VPN',
            'content' => 'Configure your VPN client with these settings.',
            'category' => 'Technical',
            'tags' => ['vpn'],
            'is_published' => true,
            'views' => 5,
        ]);

        $draft = KbArticle::create([
            'title' => 'Secret Draft',
            'content' => 'Unpublished stuff.',
            'category' => 'Technical',
            'tags' => ['secret'],
            'is_published' => false,
        ]);

        // 1. View published article
        $response = $this->get(route('kb.show', $article->slug));
        $response->assertStatus(200);
        $response->assertSee('Setting Up VPN');
        $response->assertSee('Configure your VPN client');

        // Verify view counter incremented
        $article->refresh();
        $this->assertEquals(6, $article->views);

        // 2. Attempt to view draft article (should return 404)
        $responseDraft = $this->get(route('kb.show', $draft->slug));
        $responseDraft->assertStatus(404);
    }

    /** @test */
    public function ajax_search_endpoint_returns_json_results()
    {
        $article1 = KbArticle::create([
            'title' => 'Troubleshoot database 500 error',
            'content' => 'Make sure database credentials are correct.',
            'category' => 'Technical',
            'tags' => ['database', 'error'],
            'is_published' => true,
            'views' => 15,
        ]);

        $article2 = KbArticle::create([
            'title' => 'Submitting Feedback',
            'content' => 'Submit your ideas.',
            'category' => 'Feedback',
            'tags' => ['ideas'],
            'is_published' => true,
            'views' => 30,
        ]);

        // 1. AJAX request with search query
        $response = $this->getJson(route('api.kb.search', ['q' => 'database']));
        $response->assertStatus(200);
        $response->assertJsonCount(1);
        $response->assertJsonFragment(['title' => 'Troubleshoot database 500 error']);

        // 2. AJAX request with empty search query returns default results (ordered by views)
        $responseEmpty = $this->getJson(route('api.kb.search'));
        $responseEmpty->assertStatus(200);
        // Both should return since both are published
        $responseEmpty->assertJsonCount(2);
        // The first item should be the one with higher views (Feedback, 30 views)
        $responseEmpty->assertJsonPath('0.title', 'Submitting Feedback');
        $responseEmpty->assertJsonPath('1.title', 'Troubleshoot database 500 error');
    }

    /** @test */
    public function guest_cannot_access_admin_kb_crud()
    {
        $response = $this->get(route('admin.kb-articles.index'));
        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function admin_can_perform_kb_articles_crud()
    {
        // 1. Create Article via POST
        $payload = [
            'title' => 'How to request leaves',
            'content' => 'Go to the leaves tab and click request.',
            'category' => 'General',
            'tags_input' => 'leaves, hr, guide',
            'is_published' => 1,
        ];

        $responseStore = $this->actingAs($this->adminUser)
            ->post(route('admin.kb-articles.store'), $payload);

        $responseStore->assertRedirect(route('admin.kb-articles.index'));
        
        $article = KbArticle::where('title', 'How to request leaves')->first();
        $this->assertNotNull($article);
        $this->assertEquals('how-to-request-leaves', $article->slug);
        $this->assertEquals(['leaves', 'hr', 'guide'], $article->tags);

        // 2. View Admin Index
        $responseIndex = $this->actingAs($this->adminUser)
            ->get(route('admin.kb-articles.index'));
        $responseIndex->assertStatus(200);
        $responseIndex->assertSee('How to request leaves');

        // 3. Edit view
        $responseEdit = $this->actingAs($this->adminUser)
            ->get(route('admin.kb-articles.edit', $article->id));
        $responseEdit->assertStatus(200);
        $responseEdit->assertSee('How to request leaves');

        // 4. Update Article via PUT
        $updatePayload = [
            'title' => 'How to request leaves (Updated)',
            'content' => 'Go to the leaves tab and select request type.',
            'category' => 'General',
            'tags_input' => 'leaves, hr, application',
            'is_published' => 0,
            'slug' => 'how-to-request-leaves-updated',
        ];

        $responseUpdate = $this->actingAs($this->adminUser)
            ->put(route('admin.kb-articles.update', $article->id), $updatePayload);

        $responseUpdate->assertRedirect(route('admin.kb-articles.index'));

        $article->refresh();
        $this->assertEquals('How to request leaves (Updated)', $article->title);
        $this->assertEquals('how-to-request-leaves-updated', $article->slug);
        $this->assertFalse($article->is_published);
        $this->assertEquals(['leaves', 'hr', 'application'], $article->tags);

        // 5. Delete Article via DELETE
        $responseDelete = $this->actingAs($this->adminUser)
            ->delete(route('admin.kb-articles.destroy', $article->id));

        $responseDelete->assertRedirect(route('admin.kb-articles.index'));
        $this->assertNull(KbArticle::find($article->id));
    }
}
