<?php

namespace Module\Car\Database\Seeders;

use Helper\NormalizeCharacter;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Module\Car\Models\Brand;

class BrandSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $json = File::get(base_path('module/Car/Database/data/brand.json'));
        $brands = json_decode($json, true);

        $this->createBrands($brands);
    }

    private function createBrands(array $brands, ?Brand $parent = null): void
    {
        foreach ($brands as $item) {
            $brand = $this->createBrand($item, $parent);
            if (isset($item['children'])) {
                $this->createBrands($item['children'], $brand);
            }
        }
    }

    private function createBrand(array $brand, ?Brand $parent = null): Brand
    {
        return Brand::create([
            'name' => resolve(NormalizeCharacter::class, ['text' => $brand['name']])->generate(),
            'slug' => Str::slug($brand['name']),
            'type' => $brand['type'],
            'old_id' => $brand['old_id'] ? json_encode((array) $brand['old_id']) : null,
            'parent_id' => optional($parent)->id
        ]);
    }
}
