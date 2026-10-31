<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * Controller dasar. Menyediakan helper htmx (pengganti mekanisme Livewire):
 * - isHtmx(): request meminta partial;
 * - hxRedirect(): pindah halaman penuh setelah aksi sukses (setara $this->redirect() di Livewire);
 * - hxTrigger(): memicu event di browser, mis. "close-modal".
 *
 * Untuk keamanan, method non-aksi wajib protected/private.
 */
abstract class BaseController extends Controller
{
    /**
     * @var CLIRequest|IncomingRequest
     */
    protected $request;

    /**
     * @param CLIRequest|IncomingRequest $request
     *
     * @return void
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Caution: Do not edit this line.
        parent::initController($request, $response, $logger);
    }

    protected function isHtmx(): bool
    {
        return is_htmx();
    }

    /**
     * Redirect penuh: via header HX-Redirect untuk htmx, redirect biasa untuk request non-JS.
     */
    protected function hxRedirect(string $url): RedirectResponse|ResponseInterface
    {
        if ($this->request->getHeaderLine('HX-Request') === 'true') {
            return $this->response->setHeader('HX-Redirect', $url);
        }

        return redirect()->to($url);
    }

    /**
     * Tambahkan event HX-Trigger pada respons (mis. menutup modal setelah hapus).
     */
    protected function hxTrigger(string $event): void
    {
        $this->response->setHeader('HX-Trigger', $event);
    }

    /**
     * ID user yang sedang login (dijamin ada karena route dilindungi filter auth).
     */
    protected function userId(): int
    {
        return (int) user_id();
    }
}
