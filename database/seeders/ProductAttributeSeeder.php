<?php

namespace Database\Seeders;

use App\Models\ProductAttributes;
use App\Models\ProductAttributeValues;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductAttributeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Insert attributes
        $attributes = [
            'Size',
            'Number of Spreads',
            'Print Finish',
            'Cover Materials',
            'Personalized Cover',
            'Gilding',
            'Custom Gilding',
            'Garden Collection',
            'HER Collection',
            'Vintage Collection'
        ];

        foreach ($attributes as $attributeName) {
            $attribute = ProductAttributes::create([
                'name' => $attributeName
            ]);

            // Insert corresponding values for each attribute
            switch ($attributeName) {
                case 'Size':
                    $values = [
                        ['value' => '6x6', 'price_cad' => 144, 'price_usd' => 123],
                        ['value' => '6x8', 'price_cad' => 144, 'price_usd' => 123],
                        ['value' => '8x6', 'price_cad' => 144, 'price_usd' => 123],
                        ['value' => '8x8', 'price_cad' => 204, 'price_usd' => 178],
                        ['value' => '8x10', 'price_cad' => 204, 'price_usd' => 178],
                        ['value' => '10x8', 'price_cad' => 204, 'price_usd' => 178],
                        ['value' => '10x10', 'price_cad' => 280, 'price_usd' => 247],
                        ['value' => '9x12', 'price_cad' => 280, 'price_usd' => 247],
                        ['value' => '12x9', 'price_cad' => 280, 'price_usd' => 247],
                        ['value' => '12x12', 'price_cad' => 350, 'price_usd' => 313],
                    ];
                    break;

                case 'Number of Spreads':
                    $values = array_map(function ($spread) {
                        return ['value' => $spread];
                    }, range(7, 30));
                    break;

                case 'Print Finish':
                    $values = [
                        ['value' => 'Lustre (default)', 'price_cad' => 0, 'price_usd' => 0],
                        ['value' => 'Deep Matte', 'price_cad' => 38, 'price_usd' => 29],
                        ['value' => 'Metallic', 'price_cad' => 38, 'price_usd' => 29],
                    ];
                    break;

                case 'Cover Materials':
                    $values = [
                        ['value' => 'Lilac', 'price_cad' => 0, 'price_usd' => 0],
                        ['value' => 'Olive', 'price_cad' => 0, 'price_usd' => 0],
                        ['value' => 'The Alaynna', 'price_cad' => 0, 'price_usd' => 0],
                        // Add other cover materials here
                    ];
                    break;

                case 'Personalized Cover':
                    $values = [
                        ['value' => 'None', 'price_cad' => 81, 'price_usd' => 69],
                        ['value' => 'ICE cover', 'price_cad' => 39, 'price_usd' => 31],
                    ];
                    break;

                case 'Gilding':
                    $values = [
                        ['value' => 'None', 'price_cad' => 60, 'price_usd' => 45],
                        ['value' => 'Black', 'price_cad' => 60, 'price_usd' => 45],
                        ['value' => 'Blue', 'price_cad' => 0, 'price_usd' => 0],
                        // Add other gilding options here
                    ];
                    break;

                case 'Custom Gilding':
                    $values = [
                        ['value' => 'Silver', 'price_cad' => 97, 'price_usd' => 80],
                        ['value' => 'Garden Collection', 'price_cad' => 97, 'price_usd' => 80],
                        ['value' => 'HER Collection', 'price_cad' => 97, 'price_usd' => 80],
                        ['value' => 'Vintage Collection', 'price_cad' => 80, 'price_usd' => 68],
                    ];
                    break;

                // Add other cases for Garden Collection, HER Collection, Vintage Collection, etc.

                default:
                    $values = []; // Default case if no predefined values
            }

            foreach ($values as $value) {
                ProductAttributeValues::create([
                    'attribute_id' => $attribute->id,
                    'value' => $value['value'],
                    'price_cad' => $value['price_cad'] ?? null,
                    'price_usd' => $value['price_usd'] ?? null,
                ]);
            }
        }
    }
}
