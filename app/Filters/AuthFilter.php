<?php

namespace App\Filters;

use App\Services\AuthService;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Wajib login. Mencoba login otomatis dari cookie "ingat saya" terlebih dahulu.
 * Jika gagal: simpan URL tujuan (intended), lalu arahkan ke /login.
 */
class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (user_id() !== null && current_user() !== null) {
            return null;
        }

        if ((new AuthService())->loginFromRememberCookie()) {
            return null;
        }

        $loginUrl = url_to('login');

        // htmx tidak mengikuti redirect 302 sebagai navigasi penuh — pakai HX-Redirect.
        if ($request->getHeaderLine('HX-Request') === 'true') {
            return service('response')->setStatusCode(401)->setHeader('HX-Redirect', $loginUrl);
        }

        if ($request->getMethod() === 'GET') {
            session()->set('redirect_url', current_url(true)->__toString());
        }

        return redirect()->to($loginUrl)->withCookies();
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        return null;
    }
}
