<?php

namespace Database\Seeders;

use App\Models\Items;
use App\Models\Keranjang;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Auth;

class CartSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $idItems = Items::all()->pluck('id')->toArray();
        foreach (range(1, 10) as $index) {
            Keranjang::create([
                'user_id' => 2,
                'item_id' => $idItems[array_rand($idItems)],
                'warna' => 'biru',
                'qty' => 2,
                'total' => rand(100000, 1000000)
            ]);
        }
    }
}
