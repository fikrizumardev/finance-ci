<?php

namespace Tests\Support;

use App\Database\Seeds\UserSeeder;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;

/**
 * Basis feature test: DB MySQL "tests" dimigrasi ulang & di-seed user demo,
 * CSRF dimatikan (kecuali tes yang memang menguji CSRF), cache throttle dibersihkan.
 */
abstract class FeatureTestCase extends CIUnitTestCase
{
    use DatabaseTestTrait;
    use FeatureTestTrait;

    protected $migrate     = true;
    protected $migrateOnce = true;
    protected $refresh     = true;
    protected $namespace   = 'App';
    protected $seed        = UserSeeder::class;

    protected function setUp(): void
    {
        parent::setUp();

        cache()->clean();
        $this->disableCsrf();
    }

    protected function disableCsrf(): void
    {
        $filters                    = config('Filters');
        $filters->globals['before'] = array_values(array_diff($filters->globals['before'], ['csrf']));
    }

    protected function enableCsrf(): void
    {
        config('Filters')->globals['before'][] = 'csrf';
    }

    /**
     * Header request htmx.
     *
     * @return array<string, string>
     */
    protected function htmx(): array
    {
        return ['HX-Request' => 'true'];
    }

    protected function demoUserId(): int
    {
        return (int) $this->db->table('users')->where('email', 'demo@myfinance.app')->get()->getRow()->id;
    }
}
