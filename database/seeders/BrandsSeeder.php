<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Brand;
use Illuminate\Support\Str;

class BrandsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $brandsList=array("Nike", "Gucci", "Louis Vuitton", "Adidas", "Lululemon", "Zara", "Chanel", "UNIQLO", "H&M", "Cartier", "Hermès", "Zalando", "Tiffany & Co.", "Moncler", "Rolex", "Prada", "Patek Philippe", "Burberry", "Chow Tai Fook", "Swarovski", "New Balance", "Tom Ford", "The North Face", 
			"Polo Ralph Lauren", "Levi’s", "Michael Kors", "ASOS", "Victoria’s Secret", "Under Armour", "Skechers", "Next", "Coach", "Nordstrom", "Chopard", "Macy’s", "Dolce & Gabbana", "Omega", "Christian Louboutin", "C&A", "Foot Locker Inc.", "Dior", "Ray-Ban", "Puma", 
			"Asics", "Vera Wang", "American Eagle Outfitters", "Armani", "Steve Madden", "Brunello Cucinelli", "Fendi", "Salvatore Ferragamo", "Nine West", "Urban Outfitters", "Hugo Boss", "Old Navy", "TJ Maxx", "Primark", "Max Mara", "Audemars Piguet", "IWC Schaffhausen", 
			"Diesel", "Manolo Blahnik", "Calvin Klein", "GAP", "Forever 21", "Net-a-Porter", "Longchamp", "TOD’s", "Furla", "Longines", "Sisley", "Stuart Weitzman", "Lao Feng Xiang", "Tommy Hilfiger", "Tory Burch", "Tissot", "Lacoste", "Oakley", "Jimmy Choo", "Valentino", "Patagonia", 
			"New Look", "Tag Heuer", "Cole Haan", "Topshop", "Aldo", "G-star", "Elie Tahari", "Jaeger-Le Coultre", "Fossil", "Elie Saab", "Vacheron Constantin", "Bogner", "ESCADA", "Off White", "Banana Republic", "Breguet", "Swatch", "Ted Baker", "Desigual");
			
		foreach($brandsList as $brandItem){
			$slug = Str::slug($brandItem);
			$_slugi=1;
			$_slug=$slug;
			while (Brand::where('slug', $_slug)->count()>0) {
				$_slug=$slug.$_slugi;
				$_slugi=$_slugi+1;
			}
			
			Brand::create([
            'name' => $brandItem,
            'slug' => $_slug,
			'link' => '#',
        ]);
		}
    }
}
