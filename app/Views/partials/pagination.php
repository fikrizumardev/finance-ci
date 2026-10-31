<?php
/**
 * Paginasi — port 1:1 dari view tailwind pagination Livewire 3 (markup, kelas, dan teks).
 * Navigasi halaman memakai htmx: hanya container list yang diganti, URL ikut diperbarui.
 *
 * @var \CodeIgniter\Pager\Pager $pager
 * @var string                    $baseUrl URL halaman index (bukan URL aksi)
 * @var array<string, mixed>      $query   Filter aktif yang dipertahankan (search, status, ...)
 * @var string                    $target  Selector container list, mis. "#list"
 */
$group       = $group ?? 'default';
$current     = $pager->getCurrentPage($group);
$last        = max(1, $pager->getPageCount($group));
$total       = (int) $pager->getTotal($group);
$perPage     = $pager->getPerPage($group);
$query       = array_filter($query ?? [], static fn ($v) => $v !== null && $v !== '');
$firstItem   = $total > 0 ? ($current - 1) * $perPage + 1 : null;
$lastItem    = $total > 0 ? min($current * $perPage, $total) : null;
$urlFor      = static fn (int $page): string => $baseUrl . '?' . http_build_query($query + ['page' => $page]);
$hx          = static fn (int $page): string => 'hx-get="' . esc($urlFor($page), 'attr') . '" hx-target="' . esc($target, 'attr') . '" hx-push-url="true"';

// Algoritma UrlWindow Laravel (onEachSide = 3).
$onEachSide = 3;
$range      = static fn (int $from, int $to): array => $from > $to ? [] : range($from, $to);
if ($last < ($onEachSide * 2) + 8) {
    $window = ['first' => $range(1, $last), 'slider' => null, 'last' => null];
} else {
    $size = $onEachSide + 4;
    if ($current <= $size) {
        $window = ['first' => $range(1, $size + $onEachSide), 'slider' => null, 'last' => $range($last - 1, $last)];
    } elseif ($current > ($last - $size)) {
        $window = ['first' => $range(1, 2), 'slider' => null, 'last' => $range($last - ($size + ($onEachSide - 1)), $last)];
    } else {
        $window = ['first' => $range(1, 2), 'slider' => $range($current - $onEachSide, $current + $onEachSide), 'last' => $range($last - 1, $last)];
    }
}
$elements = array_values(array_filter([
    $window['first'],
    is_array($window['slider']) ? '...' : null,
    $window['slider'],
    is_array($window['last']) ? '...' : null,
    $window['last'],
]));
?>
<div>
    <?php if ($last > 1): ?>
        <nav role="navigation" aria-label="Pagination Navigation" class="flex items-center justify-between">
            <div class="flex justify-between flex-1 sm:hidden">
                <span>
                    <?php if ($current <= 1): ?>
                        <span class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 cursor-default leading-5 rounded-md dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 dark:focus:border-blue-700 dark:active:bg-gray-700 dark:active:text-gray-300">
                            &laquo; Previous
                        </span>
                    <?php else: ?>
                        <button type="button" <?= $hx($current - 1) ?> class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 leading-5 rounded-md hover:text-gray-500 focus:outline-none focus:ring ring-blue-300 focus:border-blue-300 active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 dark:focus:border-blue-700 dark:active:bg-gray-700 dark:active:text-gray-300">
                            &laquo; Previous
                        </button>
                    <?php endif ?>
                </span>

                <span>
                    <?php if ($current < $last): ?>
                        <button type="button" <?= $hx($current + 1) ?> class="relative inline-flex items-center px-4 py-2 ml-3 text-sm font-medium text-gray-700 bg-white border border-gray-300 leading-5 rounded-md hover:text-gray-500 focus:outline-none focus:ring ring-blue-300 focus:border-blue-300 active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 dark:focus:border-blue-700 dark:active:bg-gray-700 dark:active:text-gray-300">
                            Next &raquo;
                        </button>
                    <?php else: ?>
                        <span class="relative inline-flex items-center px-4 py-2 ml-3 text-sm font-medium text-gray-500 bg-white border border-gray-300 cursor-default leading-5 rounded-md dark:text-gray-600 dark:bg-gray-800 dark:border-gray-600">
                            Next &raquo;
                        </span>
                    <?php endif ?>
                </span>
            </div>

            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                <div>
                    <p class="text-sm text-gray-700 leading-5 dark:text-gray-400">
                        <span>Showing</span>
                        <span class="font-medium"><?= $firstItem ?></span>
                        <span>to</span>
                        <span class="font-medium"><?= $lastItem ?></span>
                        <span>of</span>
                        <span class="font-medium"><?= $total ?></span>
                        <span>results</span>
                    </p>
                </div>

                <div>
                    <span class="relative z-0 inline-flex rtl:flex-row-reverse rounded-md shadow-sm">
                        <span>
                            <?php /* Previous Page Link */ ?>
                            <?php if ($current <= 1): ?>
                                <span aria-disabled="true" aria-label="&laquo; Previous">
                                    <span class="relative inline-flex items-center px-2 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 cursor-default rounded-l-md leading-5 dark:bg-gray-800 dark:border-gray-600" aria-hidden="true">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                                        </svg>
                                    </span>
                                </span>
                            <?php else: ?>
                                <button type="button" <?= $hx($current - 1) ?> class="relative inline-flex items-center px-2 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-l-md leading-5 hover:text-gray-400 focus:z-10 focus:outline-none focus:border-blue-300 focus:ring ring-blue-300 active:bg-gray-100 active:text-gray-500 transition ease-in-out duration-150 dark:bg-gray-800 dark:border-gray-600 dark:active:bg-gray-700 dark:focus:border-blue-800" aria-label="&laquo; Previous">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            <?php endif ?>
                        </span>

                        <?php /* Pagination Elements */ ?>
                        <?php foreach ($elements as $element): ?>
                            <?php /* "Three Dots" Separator */ ?>
                            <?php if (is_string($element)): ?>
                                <span aria-disabled="true">
                                    <span class="relative inline-flex items-center px-4 py-2 -ml-px text-sm font-medium text-gray-700 bg-white border border-gray-300 cursor-default leading-5 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300"><?= esc($element) ?></span>
                                </span>
                            <?php endif ?>

                            <?php /* Array Of Links */ ?>
                            <?php if (is_array($element)): ?>
                                <?php foreach ($element as $page): ?>
                                    <span>
                                        <?php if ($page === $current): ?>
                                            <span aria-current="page">
                                                <span class="relative inline-flex items-center px-4 py-2 -ml-px text-sm font-medium text-gray-500 bg-white border border-gray-300 cursor-default leading-5 dark:bg-gray-800 dark:border-gray-600"><?= $page ?></span>
                                            </span>
                                        <?php else: ?>
                                            <button type="button" <?= $hx($page) ?> class="relative inline-flex items-center px-4 py-2 -ml-px text-sm font-medium text-gray-700 bg-white border border-gray-300 leading-5 hover:text-gray-500 focus:z-10 focus:outline-none focus:border-blue-300 focus:ring ring-blue-300 active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-400 dark:hover:text-gray-300 dark:active:bg-gray-700 dark:focus:border-blue-800" aria-label="Go to page <?= $page ?>">
                                                <?= $page ?>
                                            </button>
                                        <?php endif ?>
                                    </span>
                                <?php endforeach ?>
                            <?php endif ?>
                        <?php endforeach ?>

                        <span>
                            <?php /* Next Page Link */ ?>
                            <?php if ($current < $last): ?>
                                <button type="button" <?= $hx($current + 1) ?> class="relative inline-flex items-center px-2 py-2 -ml-px text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-r-md leading-5 hover:text-gray-400 focus:z-10 focus:outline-none focus:border-blue-300 focus:ring ring-blue-300 active:bg-gray-100 active:text-gray-500 transition ease-in-out duration-150 dark:bg-gray-800 dark:border-gray-600 dark:active:bg-gray-700 dark:focus:border-blue-800" aria-label="Next &raquo;">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            <?php else: ?>
                                <span aria-disabled="true" aria-label="Next &raquo;">
                                    <span class="relative inline-flex items-center px-2 py-2 -ml-px text-sm font-medium text-gray-500 bg-white border border-gray-300 cursor-default rounded-r-md leading-5 dark:bg-gray-800 dark:border-gray-600" aria-hidden="true">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                        </svg>
                                    </span>
                                </span>
                            <?php endif ?>
                        </span>
                    </span>
                </div>
            </div>
        </nav>
    <?php endif ?>
</div>
