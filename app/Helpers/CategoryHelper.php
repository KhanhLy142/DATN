<?php

namespace App\Helpers;

use App\Models\Category;

class CategoryHelper
{
    public static function getMenuCategories()
    {
        return cache()->remember('menu_categories', 3600, function() {
            return Category::where('status', 1)
                ->whereNull('parent_id')
                ->with(['children' => function($query) {
                    $query->where('status', 1)
                        ->orderBy('name', 'asc');
                }])
                ->orderBy('name', 'asc')
                ->get();
        });
    }

    public static function clearMenuCache()
    {
        cache()->forget('menu_categories');
    }

    public static function getBreadcrumb($categoryId)
    {
        $category = Category::find($categoryId);
        $breadcrumb = [];

        if ($category) {
            // Nếu là danh mục con, thêm danh mục cha trước
            if ($category->parent) {
                $breadcrumb[] = [
                    'name' => $category->parent->name,
                    'url' => route('category.show', $category->parent->id)
                ];
            }

            $breadcrumb[] = [
                'name' => $category->name,
                'url' => route('category.show', $category->id),
                'current' => true
            ];
        }

        return $breadcrumb;
    }

    public static function getCategoryWithChildren($categoryId)
    {
        $category = Category::find($categoryId);
        $categoryIds = [$categoryId];

        if ($category && !$category->parent_id) {
            $childrenIds = Category::where('parent_id', $categoryId)
                ->where('status', 1)
                ->pluck('id')
                ->toArray();

            $categoryIds = array_merge($categoryIds, $childrenIds);
        }

        return $categoryIds;
    }
}
