<?php

namespace Database\Seeders;

use App\Models\Items;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ItemsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $randomRanselPhotoUrl = '/storage/items-image/tas';
        for ($i = 0; $i < 5; $i++) {
            $item = new Items();
            $item->nama = 'Item ' . $i;
            $item->harga = rand(100000, 1000000);
            $item->description = 'Deskripsi item ' . $i;
            $item->warna = 'biru,merah,hijau';
            $item->stok = rand(10, 100);
            $item->image = $randomRanselPhotoUrl . rand(2, 3) . '.jpeg';
            $item->save();
        }
    }
}
