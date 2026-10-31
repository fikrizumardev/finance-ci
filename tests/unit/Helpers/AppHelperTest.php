<?php

namespace Tests\Unit\Helpers;

use CodeIgniter\Test\CIUnitTestCase;

/**
 * @internal
 */
final class AppHelperTest extends CIUnitTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        helper('app');
    }

    public function testRupiah(): void
    {
        $this->assertSame('Rp 1.234.567', rupiah(1234567));
        $this->assertSame('Rp 350.000', rupiah('350000.00'));
        $this->assertSame('Rp 0', rupiah(null));
        $this->assertSame('Rp 1.000', rupiah(999.5));
    }

    public function testMonthName(): void
    {
        $this->assertSame('Januari', month_name(1));
        $this->assertSame('Desember', month_name(12));
        $this->assertSame('', month_name(13));
        $this->assertCount(12, month_names());
    }

    public function testIsHtmx(): void
    {
        $this->assertFalse(is_htmx());

        service('request')->setHeader('HX-Request', 'true');
        $this->assertTrue(is_htmx());
    }

    public function testFieldErrorRendersMessageFromValidator(): void
    {
        $this->assertSame('', field_error('name'));
        $this->assertFalse(has_error('name'));

        service('validation')->setError('name', 'Nama tagihan wajib diisi.');

        $this->assertTrue(has_error('name'));
        $this->assertSame('Nama tagihan wajib diisi.', error_message('name'));
        $this->assertSame('<p class="mt-1 text-xs text-red-600">Nama tagihan wajib diisi.</p>', field_error('name'));
    }

    public function testFieldErrorEscapesMessage(): void
    {
        service('validation')->setError('notes', '<b>x</b>');

        $this->assertStringContainsString('&lt;b&gt;x&lt;/b&gt;', field_error('notes'));
    }
}
