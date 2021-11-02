<?php

namespace Module\Car\Database\Seeders;

use Helper\NormalizeCharacter;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Module\Car\Models\Brand;
use Module\Region\Models\Country;

class BrandSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $json = File::get("module/Car/Database/data/brand.json");
        $data = json_decode($json, true);

        foreach ($data as $obj) {
            $grandpaBrand = Brand::create([
                'name'    => resolve(NormalizeCharacter::class, ['text' => $obj["name"]])->generate(),
                'name_en' => Str::slug($obj["name"]),
                'type'    => $obj["type"],
                'old_id'  => !is_null($obj["old_id"]) ? (is_array($obj["old_id"]) ? json_encode($obj["old_id"]) : json_encode([$obj["old_id"]])) : null,
            ]);
            if (isset($obj['children'])) {
                foreach ($obj['children'] as $children) {
                    $parentBrand = Brand::create([
                        'name'      => resolve(NormalizeCharacter::class, ['text' => $children["name"]])->generate(),
                        'name_en'   => Str::slug($children["name"]),
                        'type'      => $children["type"],
                        'old_id'    => !is_null($children["old_id"]) ? (is_array($children["old_id"]) ? json_encode($children["old_id"]) : json_encode([$children["old_id"]])) : null,
                        'parent_id' => $grandpaBrand->id
                    ]);
                    if (isset($children['children'])) {
                        foreach ($children['children'] as $child) {
                            Brand::create([
                                'name'      => resolve(NormalizeCharacter::class, ['text' => $child["name"]])->generate(),
                                'name_en'   => Str::slug($child["name"]),
                                'type'      => $child["type"],
                                'old_id'    => !is_null($child["old_id"]) ? (is_array($child["old_id"]) ? json_encode($child["old_id"]) : json_encode([$child["old_id"]])) : null,
                                'parent_id' => $parentBrand->id
                            ]);
                        }
                    }
                }
            }
        }
    }
}
