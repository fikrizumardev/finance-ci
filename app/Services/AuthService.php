<?php

namespace App\Services;

use App\Entities\User;
use App\Models\UserModel;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\I18n\Time;

/**
 * Autentikasi berbasis session di atas tabel users milik Laravel
 * (hash bcrypt Laravel langsung valid dengan password_verify()).
 */
class AuthService
{
    public const REMEMBER_COOKIE = 'remember_web';

    private const MAX_ATTEMPTS   = 5;
    private const DECAY_SECONDS  = 60;
    private const REMEMBER_DAYS  = 30;

    public function __construct(private readonly UserModel $users = new UserModel())
    {
    }

    /**
     * Coba login. Mengembalikan pesan error untuk field email, atau null jika berhasil.
     */
    public function attempt(string $email, string $password, bool $remember, string $ip): ?string
    {
        $key = $this->throttleKey($email, $ip);

        if (($seconds = $this->lockoutSeconds($key)) > 0) {
            return "Terlalu banyak percobaan login. Coba lagi dalam {$seconds} detik.";
        }

        $user = $this->users->findByEmail($email);

        if ($user === null || ! password_verify($password, $user->password)) {
            $this->hit($key);

            return 'Email atau password salah.';
        }

        cache()->delete($key);
        $this->login($user, $remember);

        return null;
    }

    public function login(User $user, bool $remember = false): void
    {
        session()->regenerate(true);
        session()->set('user_id', (int) $user->id);

        if ($remember) {
            $this->issueRememberToken($user);
        }
    }

    public function logout(): void
    {
        $id = user_id();

        if ($id !== null) {
            $this->users->update($id, ['remember_token' => null]);
        }

        service('response')->deleteCookie(self::REMEMBER_COOKIE);
        session()->remove('user_id');
        session()->destroy();
    }

    /**
     * Login otomatis dari cookie "ingat saya". Mengembalikan true jika berhasil.
     */
    public function loginFromRememberCookie(): bool
    {
        $cookie = (string) service('request')->getCookie(self::REMEMBER_COOKIE);

        if (! str_contains($cookie, '|')) {
            return false;
        }

        [$id, $token] = explode('|', $cookie, 2);
        $user         = ctype_digit($id) ? $this->users->find((int) $id) : null;

        if ($user === null || empty($user->remember_token) || ! hash_equals($user->remember_token, hash('sha256', $token))) {
            service('response')->deleteCookie(self::REMEMBER_COOKIE);

            return false;
        }

        session()->regenerate(true);
        session()->set('user_id', (int) $user->id);

        return true;
    }

    private function issueRememberToken(User $user): void
    {
        $token = bin2hex(random_bytes(30));

        $this->users->update($user->id, ['remember_token' => hash('sha256', $token)]);

        service('response')->setCookie(
            self::REMEMBER_COOKIE,
            $user->id . '|' . $token,
            self::REMEMBER_DAYS * DAY,
            httponly: true,
            samesite: 'Lax',
        );
    }

    /**
     * Sama dengan throttleKey() Livewire Login: lower(email)|ip.
     */
    private function throttleKey(string $email, string $ip): string
    {
        return 'login_' . md5(mb_strtolower($email) . '|' . $ip);
    }

    private function lockoutSeconds(string $key): int
    {
        $state = cache($key);

        if (! is_array($state) || $state['attempts'] < self::MAX_ATTEMPTS) {
            return 0;
        }

        return max(1, $state['expires'] - Time::now()->getTimestamp());
    }

    private function hit(string $key): void
    {
        $now   = Time::now()->getTimestamp();
        $state = cache($key);

        if (! is_array($state) || $state['expires'] <= $now) {
            $state = ['attempts' => 0, 'expires' => $now + self::DECAY_SECONDS];
        }

        $state['attempts']++;
        cache()->save($key, $state, max(1, $state['expires'] - $now));
    }
}
