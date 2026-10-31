<?php

namespace Tests\Unit\Entities;

use App\Entities\BillPayment;
use App\Entities\Debt;
use App\Entities\User;
use App\Entities\UtilityBill;
use App\Enums\BillCategory;
use App\Enums\DebtStatus;
use App\Enums\PaymentMethod;
use CodeIgniter\I18n\Time;
use CodeIgniter\Test\CIUnitTestCase;

/**
 * @internal
 */
final class EntityTest extends CIUnitTestCase
{
    public function testUserInitials(): void
    {
        $this->assertSame('DU', (new User(['name' => 'Demo User']))->initials());
        $this->assertSame('A', (new User(['name' => 'Admin']))->initials());
        $this->assertSame('FZR', (new User(['name' => 'Fikri Zumar Ramadhan']))->initials());
    }

    public function testUtilityBillCastsCategoryToEnum(): void
    {
        $bill = new UtilityBill(['category' => 'electricity', 'is_active' => '1', 'due_day' => '20']);

        $this->assertSame(BillCategory::Electricity, $bill->category);
        $this->assertSame('⚡', $bill->category->icon());
        $this->assertTrue($bill->is_active);
        $this->assertSame(20, $bill->due_day);
    }

    /**
     * @dataProvider dueSoonProvider
     */
    public function testUtilityBillIsDueSoon(?int $dueDay, string $today, bool $expected): void
    {
        $bill = new UtilityBill(['due_day' => $dueDay]);

        $this->assertSame($expected, $bill->isDueSoon(3, Time::parse($today)));
    }

    /**
     * Logika sama dengan Laravel: hanya melihat sisa hari di bulan yang sama (tidak lintas bulan).
     *
     * @return iterable<string, array{int|null, string, bool}>
     */
    public static function dueSoonProvider(): iterable
    {
        yield 'tanpa jatuh tempo' => [null, '2026-10-18', false];
        yield 'hari ini jatuh tempo' => [20, '2026-10-20', true];
        yield 'tiga hari lagi' => [20, '2026-10-17', true];
        yield 'empat hari lagi' => [20, '2026-10-16', false];
        yield 'sudah lewat' => [20, '2026-10-21', false];
        yield 'akhir bulan tidak lintas bulan' => [1, '2026-10-30', false];
    }

    public function testBillPaymentPeriodLabelAndCasts(): void
    {
        $payment = new BillPayment([
            'period_month'    => '6',
            'period_year'     => '2026',
            'payment_date'    => '2026-06-20',
            'payment_method'  => 'qris',
            'screenshot_path' => null,
        ]);

        $this->assertSame('Juni 2026', $payment->period_label);
        $this->assertSame(PaymentMethod::Qris, $payment->payment_method);
        $this->assertSame('20 Jun 2026', $payment->payment_date->format('d M Y'));
        $this->assertFalse($payment->hasScreenshot());

        $payment->screenshot_path = 'bill-payments/a.jpg';
        $this->assertTrue($payment->hasScreenshot());
    }

    public function testDebtNullableDueDateAndStatusEnum(): void
    {
        $debt = new Debt(['status' => 'partial', 'borrowed_date' => '2026-01-05', 'due_date' => null]);

        $this->assertSame(DebtStatus::PARTIAL, $debt->status);
        $this->assertNull($debt->due_date);
        $this->assertSame('2026-01-05', $debt->borrowed_date->format('Y-m-d'));
    }
}
