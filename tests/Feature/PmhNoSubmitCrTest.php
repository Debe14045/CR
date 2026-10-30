<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Client;
use App\Models\ChangeRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PmhNoSubmitCrTest extends TestCase
{
    use RefreshDatabase;

    private User $pmh;
    private User $pm;
    private User $clientUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->pmh = User::firstOrCreate(
            ['email' => 'pmhead@itpi.test'],
            [
                'name' => 'PM Head User',
                'password' => 'password',
                'role' => 'pmh',
                'is_active' => true,
            ]
        );

        $this->pm = User::firstOrCreate(
            ['email' => 'pm@itpi.test'],
            [
                'name' => 'PM User',
                'password' => 'password',
                'role' => 'pm',
                'is_active' => true,
            ]
        );

        $this->clientUser = User::firstOrCreate(
            ['email' => 'client@test.com'],
            [
                'name' => 'Client User',
                'password' => 'password',
                'role' => 'client',
                'is_active' => true,
            ]
        );

        Client::create([
            'name' => 'Client Company',
            'company' => 'PT Client Company',
            'email' => 'client@test.com',
            'phone' => '08123456789',
            'active' => true,
        ]);
    }

    public function test_pmh_cannot_see_submit_new_cr_button_on_dashboard(): void
    {
        $response = $this->actingAs($this->pmh)->get(route('pmh.dashboard'));
        $response->assertOk();
        $content = $response->getContent();
        $htmlWithoutScripts = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $content);
        $this->assertStringNotContainsString('Ajukan CR Baru', $htmlWithoutScripts);
        $response->assertDontSee(route('change-requests.create'));
    }

    public function test_pmh_redirects_directly_to_pmh_dashboard(): void
    {
        // 1. Root / redirects to pmh.dashboard
        $responseRoot = $this->actingAs($this->pmh)->get('/');
        $responseRoot->assertRedirect(route('pmh.dashboard'));

        // 2. /dashboard redirects to pmh.dashboard
        $responseDashboard = $this->actingAs($this->pmh)->get('/dashboard');
        $responseDashboard->assertRedirect(route('pmh.dashboard'));

        // 3. /change-requests directly redirects to pmh.dashboard
        $responseCr = $this->actingAs($this->pmh)->get(route('change-requests.index'));
        $responseCr->assertRedirect(route('pmh.dashboard'));

        // 4. Login redirects to pmh.dashboard
        \Illuminate\Support\Facades\Auth::logout();
        session()->flush();
        $responseLogin = $this->post('/login', [
            'email' => $this->pmh->email,
            'password' => 'password',
        ]);
        $responseLogin->assertRedirect(route('pmh.dashboard'));
    }

    public function test_pmh_cannot_access_create_cr_route(): void
    {
        $response = $this->actingAs($this->pmh)->get(route('change-requests.create'));
        $response->assertStatus(403);
    }

    public function test_pmh_cannot_submit_new_cr_post(): void
    {
        $response = $this->actingAs($this->pmh)->post(route('change-requests.store'), [
            'judul' => 'CR From PM Head Attempt',
            'proyek_terkait' => 'Proyek Test',
            'deskripsi' => 'Deskripsi CR',
            'action' => 'submit',
        ]);
        $response->assertStatus(403);
    }

    public function test_pm_can_access_create_cr_and_sees_button(): void
    {
        $response = $this->actingAs($this->pm)->get(route('change-requests.index'));
        $response->assertOk();
        $response->assertSee('Ajukan CR Baru');

        $responseCreate = $this->actingAs($this->pm)->get(route('change-requests.create'));
        $responseCreate->assertOk();
    }

    public function test_client_can_access_create_cr(): void
    {
        $responseCreate = $this->actingAs($this->clientUser)->get(route('change-requests.create'));
        $responseCreate->assertOk();
    }

    public function test_client_does_not_see_cr_tabs(): void
    {
        $response = $this->actingAs($this->clientUser)->get(route('change-requests.index', ['view' => 'total']));
        $response->assertOk();
        $content = $response->getContent();
        $htmlWithoutScripts = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $content);
        $this->assertStringNotContainsString('CR Aktif', $htmlWithoutScripts);
        $this->assertStringNotContainsString('tab_active_cr', $htmlWithoutScripts);
    }

    public function test_pm_sees_cr_tabs(): void
    {
        $response = $this->actingAs($this->pm)->get(route('change-requests.index', ['view' => 'total']));
        $response->assertOk();
        $response->assertSee('tab_active_cr');
        $response->assertSee('CR Aktif');
    }
}
