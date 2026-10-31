<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Services\AuthService;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Port dari App\Livewire\Auth\Login, Register, dan App\Livewire\Actions\Logout.
 */
class AuthController extends BaseController
{
    private AuthService $auth;

    public function __construct()
    {
        $this->auth = new AuthService();
    }

    public function login(): string
    {
        return view('auth/login', ['form' => []]);
    }

    public function attempt(): string|RedirectResponse|ResponseInterface
    {
        $form = [
            'email'    => trim((string) $this->request->getPost('email')),
            'password' => (string) $this->request->getPost('password'),
            'remember' => (bool) $this->request->getPost('remember'),
        ];

        $valid = $this->validateData($form, [
            'email'    => 'required|valid_email',
            'password' => 'required',
        ], [
            'email'    => ['required' => 'Email wajib diisi.', 'valid_email' => 'Format email tidak valid.'],
            'password' => ['required' => 'Password wajib diisi.'],
        ]);

        if ($valid) {
            $error = $this->auth->attempt($form['email'], $form['password'], $form['remember'], (string) $this->request->getIPAddress());

            if ($error === null) {
                $intended = session('redirect_url') ?: url_to('dashboard');
                session()->remove('redirect_url');

                return $this->hxRedirect($intended);
            }

            $this->validator->setError('email', $error);
        }

        return $this->isHtmx()
            ? view('auth/_login_form', ['form' => $form])
            : view('auth/login', ['form' => $form]);
    }

    public function logout(): RedirectResponse
    {
        $this->auth->logout();

        return redirect()->to('/')->withCookies();
    }

    public function register(): string
    {
        return view('auth/register', ['form' => []]);
    }

    public function store(): string|RedirectResponse|ResponseInterface
    {
        $form = [
            'name'                  => trim((string) $this->request->getPost('name')),
            'email'                 => trim((string) $this->request->getPost('email')),
            'password'              => (string) $this->request->getPost('password'),
            'password_confirmation' => (string) $this->request->getPost('password_confirmation'),
        ];

        $valid = $this->validateData($form, [
            'name'     => 'required|max_length[255]',
            'email'    => 'required|valid_email|max_length[255]|is_unique[users.email]',
            'password' => 'required|min_length[8]',
        ], [
            'name'     => ['required' => 'Nama wajib diisi.'],
            'email'    => [
                'required'    => 'Email wajib diisi.',
                'valid_email' => 'Format email tidak valid.',
                'is_unique'   => 'Email sudah terdaftar.',
            ],
            'password' => ['required' => 'Password wajib diisi.'],
        ]);

        if ($form['password'] !== '' && $form['password'] !== $form['password_confirmation'] && ! has_error('password')) {
            $this->validator->setError('password', 'Konfirmasi password tidak cocok.');
            $valid = false;
        }

        if (! $valid) {
            return $this->isHtmx()
                ? view('auth/_register_form', ['form' => $form])
                : view('auth/register', ['form' => $form]);
        }

        $users = new UserModel();
        $id    = $users->insert([
            'name'     => $form['name'],
            'email'    => $form['email'],
            'password' => password_hash($form['password'], PASSWORD_BCRYPT),
        ]);

        $this->auth->login($users->find($id));

        return $this->hxRedirect(url_to('dashboard'));
    }
}
