<?php

namespace Tests\Unit\Views;

use CodeIgniter\Pager\Pager;
use CodeIgniter\Test\CIUnitTestCase;

/**
 * @internal
 */
final class PaginationPartialTest extends CIUnitTestCase
{
    private function render(int $page, int $total, array $query = ['search' => 'pln']): string
    {
        /** @var Pager $pager */
        $pager = service('pager', null, null, false);
        $pager->store('default', $page, 15, $total);

        return view('partials/pagination', [
            'pager'   => $pager,
            'baseUrl' => 'http://localhost:8080/house/bills',
            'query'   => $query,
            'target'  => '#list',
        ]);
    }

    public function testRendersNothingForSinglePage(): void
    {
        $this->assertStringNotContainsString('<nav', $this->render(1, 10));
    }

    public function testSummaryAndLinksKeepFilters(): void
    {
        $html = $this->render(2, 40);

        $this->assertMatchesRegularExpression('/Showing<\/span>\s*<span class="font-medium">16<\/span>\s*<span>to<\/span>\s*<span class="font-medium">30<\/span>\s*<span>of<\/span>\s*<span class="font-medium">40<\/span>/', $html);
        $this->assertStringContainsString('hx-get="http&#x3A;&#x2F;&#x2F;localhost&#x3A;8080&#x2F;house&#x2F;bills&#x3F;search&#x3D;pln&amp;page&#x3D;3"', $html);
        $this->assertStringContainsString('hx-target="&#x23;list"', $html);
        $this->assertStringContainsString('<span aria-current="page">', $html);
    }

    public function testEmptyFiltersAreDropped(): void
    {
        $html = $this->render(1, 40, ['search' => '', 'status' => null]);

        $this->assertStringContainsString('bills&#x3F;page&#x3D;2"', $html);
    }

    public function testUrlWindowShowsSeparatorsForManyPages(): void
    {
        // 30 halaman, posisi tengah → 1 2 ... 12..18 ... 29 30 (sama dengan UrlWindow Laravel).
        $html = $this->render(15, 450);

        preg_match_all('/aria-label="Go to page (\d+)"|<span aria-current="page">\s*<span[^>]*>(\d+)</', $html, $m);
        $pages = array_map('intval', array_filter(array_merge($m[1], $m[2])));
        sort($pages);

        $this->assertSame([1, 2, 12, 13, 14, 15, 16, 17, 18, 29, 30], $pages);
        $this->assertSame(2, substr_count($html, '>...</span>'));
    }
}
