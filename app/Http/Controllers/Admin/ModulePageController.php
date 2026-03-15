<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Arr;

class ModulePageController extends Controller
{
    public function __invoke(string $module): View
    {
        $pages = collect(config('admin.navigation', []))
            ->flatMap(fn (array $section) => $section['items'] ?? [])
            ->flatMap(function (array $item): array {
                if (isset($item['children'])) {
                    return $item['children'];
                }

                return [$item];
            })
            ->keyBy('slug');

        abort_unless($pages->has($module), 404);

        $page = $pages->get($module);

        return view('admin.modules.show', [
            'page' => $page,
            'module' => $module,
            'sectionTitle' => Arr::get($page, 'section', 'Admin'),
        ]);
    }
}
