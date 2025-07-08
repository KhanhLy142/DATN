<?php

namespace App\Helpers;

use App\Models\Brand;
use App\Models\Category;

class ProductFilterHelper
{
    public static function getCategoryPageFilterData()
    {
        return [
            'brands' => self::getBrands(),
            'priceRanges' => self::getPriceRanges()
        ];
    }

    public static function getProductPageFilterData()
    {
        return [
            'categories' => self::getCategories(),
            'brands' => self::getBrands(),
            'priceRanges' => self::getPriceRanges()
        ];
    }
    public static function getFilterData()
    {
        return [
            'categories' => self::getCategories(),
            'brands' => self::getBrands(),
            'priceRanges' => self::getPriceRanges()
        ];
    }

    public static function getCategories()
    {
        try {
            $categories = Category::where('status', 1)
                ->whereNull('parent_id')
                ->select('id', 'name')
                ->orderBy('name', 'asc')
                ->get();

            return $categories->map(function($category) {
                return [
                    'value' => $category->id,
                    'label' => $category->name,
                ];
            })->toArray();

        } catch (\Exception $e) {
            \Log::error('Error getting categories: ' . $e->getMessage());

            return [
                ['value' => 1, 'label' => 'Chăm sóc da'],
                ['value' => 2, 'label' => 'Trang điểm'],
                ['value' => 3, 'label' => 'Nước hoa'],
            ];
        }
    }

    public static function getBrands()
    {
        try {
            $brands = Brand::where('status', 1)
                ->whereHas('products', function($query) {
                    $query->where('status', 1)
                        ->where('stock', '>', 0);
                })
                ->select('id', 'name')
                ->orderBy('name', 'asc')
                ->get();

            return $brands->map(function($brand) {
                return [
                    'value' => $brand->name,
                    'label' => $brand->name
                ];
            })->toArray();

        } catch (\Exception $e) {
            \Log::error('Error getting brands: ' . $e->getMessage());

            return [
                ['value' => 'Bioré', 'label' => 'Bioré'],
                ['value' => 'CeraVe', 'label' => 'CeraVe'],
                ['value' => 'Innisfree', 'label' => 'Innisfree'],
                ['value' => 'COSRX', 'label' => 'COSRX'],
                ['value' => 'La Roche-Posay', 'label' => 'La Roche-Posay']
            ];
        }
    }

    public static function getPriceRanges()
    {
        return [
            ['value' => '', 'label' => 'Tất cả mức giá'],
            ['value' => '0-100000', 'label' => 'Dưới 100.000đ'],
            ['value' => '100000-300000', 'label' => '100.000đ - 300.000đ'],
            ['value' => '300000-500000', 'label' => '300.000đ - 500.000đ'],
            ['value' => '500000-1000000', 'label' => '500.000đ - 1.000.000đ'],
            ['value' => '1000000-99999999', 'label' => 'Trên 1.000.000đ']
        ];
    }

    public static function getCachedFilterData()
    {
        return cache()->remember('filter_data', 3600, function() {
            return self::getFilterData();
        });
    }

    public static function clearFilterCache()
    {
        cache()->forget('filter_data');
    }

}
