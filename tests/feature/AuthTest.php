<?php

namespace Tests\Feature;

use App\Services\AuthService;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\Security\Exceptions\SecurityException;
use Tests\Support\FeatureTestCase;

/**
 * @internal
 */
final class AuthTest extends FeatureTestCase
{
    public function testGuestIsRedirectedToLoginAndIntendedUrlIsRemembered(): void
    {
        $result = $this->get('house/bills?search=pln');

        $result->assertRedirectTo(url_to('login'));
        $this->assertStringEndsWith('house/bills?search=pln', session('redirect_url'));
    }

    public function testHtmxGuestRequestGetsHxRedirect(): void
    {
        $result = $this->withHeaders($this->htmx())->get('house/bills');

        $result->assertStatus(401);
        $result->assertHeader('HX-Redirect', url_to('login'));
    }

    public function testLoginPageRenders(): void
    {
        $result = $this->get('login');

        $result->assertOK();
        $result->assertSee('Selamat datang kembali');
        $result->assertSee('Ingat saya di perangkat ini');
    }

    public function testValidationErrorsAreReturnedAsFormPartial(): void
    {
        $result = $this->withHeaders($this->htmx())->post('login', ['email' => '', 'password' => '']);

        $result->assertOK();
        $result->assertSee('Email wajib diisi.');
        $result->assertSee('Password wajib diisi.');
        $this->assertStringNotContainsString('<html', (string) $result->response()->getBody());
    }

    public function testInvalidEmailFormat(): void
    {
        $this->withHeaders($this->htmx())->post('login', ['email' => 'bukan-email', 'password' => 'x'])
            ->assertSee('Format email tidak valid.');
    }

    public function testWrongPasswordShowsErrorAndKeepsEmail(): void
    {
        $result = $this->withHeaders($this->htmx())->post('login', ['email' => 'demo@myfinance.app', 'password' => 'salah']);

        $result->assertSee('Email atau password salah.');
        $result->assertSee('value="demo@myfinance.app"', null);
        $this->assertNull(session('user_id'));
    }

    public function testSuccessfulLoginRedirectsToDashboard(): void
    {
        $result = $this->withHeaders($this->htmx())->post('login', ['email' => 'demo@myfinance.app', 'password' => 'password']);

        $result->assertHeader('HX-Redirect', url_to('dashboard'));
        $this->assertSame($this->demoUserId(), session('user_id'));
    }

    public function testSuccessfulLoginRedirectsToIntendedUrl(): void
    {
        $result = $this->withSession(['redirect_url' => site_url('house/bills')])
            ->withHeaders($this->htmx())
            ->post('login', ['email' => 'demo@myfinance.app', 'password' => 'password']);

        $result->assertHeader('HX-Redirect', site_url('house/bills'));
    }

    public function testNonHtmxLoginUsesNormalRedirect(): void
    {
        $this->post('login', ['email' => 'demo@myfinance.app', 'password' => 'password'])
            ->assertRedirectTo(url_to('dashboard'));
    }

    public function testLoginIsThrottledAfterFiveFailedAttempts(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $this->withHeaders($this->htmx())->post('login', ['email' => 'demo@myfinance.app', 'password' => 'salah'])
                ->assertSee('Email atau password salah.');
        }

        // Password benar pun ditolak selama terkunci.
        $result = $this->withHeaders($this->htmx())->post('login', ['email' => 'demo@myfinance.app', 'password' => 'password']);

        $result->assertSee('Terlalu banyak percobaan login. Coba lagi dalam');
        $this->assertNull(session('user_id'));
    }

    public function testRememberMeSetsCookieAndAutoLogsIn(): void
    {
        $result = $this->withHeaders($this->htmx())
            ->post('login', ['email' => 'demo@myfinance.app', 'password' => 'password', 'remember' => '1']);

        $result->assertCookie(AuthService::REMEMBER_COOKIE);
        $cookie = $result->response()->getCookie(AuthService::REMEMBER_COOKIE)->getValue();

        // Sesi baru (tanpa user_id) dengan cookie "ingat saya" → login otomatis.
        session()->remove('user_id');
        service('superglobals')->setCookie(AuthService::REMEMBER_COOKIE, $cookie);

        $this->withHeaders([])->get('dashboard')->assertOK();
        $this->assertSame($this->demoUserId(), session('user_id'));
    }

    public function testTamperedRememberCookieIsRejected(): void
    {
        service('superglobals')->setCookie(AuthService::REMEMBER_COOKIE, $this->demoUserId() . '|token-palsu');

        $this->get('dashboard')->assertRedirectTo(url_to('login'));
    }

    public function testLogout(): void
    {
        $result = $this->withSession(['user_id' => $this->demoUserId()])->post('logout');

        $result->assertRedirectTo(site_url('/'));
        $this->assertNull(session('user_id'));
    }

    public function testLoggedInUserCannotSeeLoginPage(): void
    {
        $this->withSession(['user_id' => $this->demoUserId()])->get('login')
            ->assertRedirectTo(url_to('dashboard'));
    }

    public function testPostWithoutCsrfTokenIsRejected(): void
    {
        $this->enableCsrf();
        $this->expectException(SecurityException::class); // di HTTP sungguhan menjadi respons 403

        $this->withHeaders($this->htmx())
            ->post('login', ['email' => 'demo@myfinance.app', 'password' => 'password']);
    }

    public function testRegistrationIsDisabledByDefault(): void
    {
        $this->expectException(PageNotFoundException::class);

        $this->get('register');
    }
}
