<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Tag;
use Illuminate\Support\Str;

class TagsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $brandsList=array("A-line dress", "Ball gown", "Bodycon dress", "Cocktail dress", "Empire waist dress", "Fit and flare dress", "Maxi dress", "Midi dress", "Mini dress", "Peplum dress", "Sheath dress",
"Shift dress", "Shirt dress", "Skater dress", "Slip dress", "Sundress", "Tunic dress", "Wrap dress", "Casual outfit", "Formal outfit", "Business attire", "Athleisure outfit",
"Beachwear", "Evening gown", "Party outfit", "Summer outfit", "Winter outfit", "Bohemian outfit", "Streetwear", "Retro/vintage outfit", "Punk outfit", "Rocker outfit", 
"Preppy outfit", "Sportswear", "Ethnic/cultural outfit", "Military-inspired outfit", "Baseball cap", "Fedora", "Bucket hat", "Sun hat", "Beanie", "Newsboy cap", "Cowboy hat",
"Panama hat", "Beret", "Cloche hat", "Jeans", "Leggings", "Trousers", "Chinos", "Palazzo pants", "Culottes", "Cargo pants", "Capri pants", "Jumpsuit", "Harem pants", 
"Mini skirt", "Midi skirt", "Maxi skirt", "Pencil skirt", "A-line skirt", "Pleated skirt", "Skater skirt", "Wrap skirt", "Denim skirt", "Tutu skirt", "Ball gown wedding dress", 
"A-line wedding dress", "Mermaid/trumpet wedding dress", "Sheath wedding dress", "Tea-length wedding dress", "Empire waist wedding dress", "Princess wedding dress", "Backless wedding dress",
"Off-the-shoulder wedding dress", "Lace wedding dress", "Sleeveless jumpsuit", "Wide-leg jumpsuit", "Strapless jumpsuit", "Floral romper", "Denim jumpsuit", "Culotte jumpsuit",
"Off-the-shoulder jumpsuit", "Kimono (Japanese)", "Sari (Indian)", "Hanbok (Korean)", "Cheongsam/Qipao (Chinese)", "Dashiki (African)", "Dirndl (German)", "Kilt (Scottish)",
"Thobe (Middle Eastern)", "Ao dai (Vietnamese)", "Trachten (Austrian)", "Bikini", "One-piece swimsuit", "Tankini", "Monokini", "High-waisted bikini", "Swim shorts", 
"Rash guard", "Bra and panty set", "Corset", "Babydoll", "Chemise", "Teddy", "Camisole", "Bodysuit", "Sports bra", "Leggings", "Yoga pants", "Sweatpants", "Athletic shorts",
"Tank top", "Hoodie", "Athletic shoes", "Superhero costume", "Fairy/princess costume", "Pirate costume", "Halloween costume", "Animal costume", "Historical costume",
"Movie character costume", "Military uniform", "Police uniform", "Nurse uniform", "School uniform", "Chef uniform", "Flight attendant uniform", "Sports team uniform");
			
		foreach($brandsList as $brandItem){
			$slug = Str::slug($brandItem);
			$_slugi=1;
			$_slug=$slug;
			while (Tag::where('slug', $_slug)->count()>0) {
				$_slug=$slug.$_slugi;
				$_slugi=$_slugi+1;
			}
			
			Tag::create([
            'name' => $brandItem,
            'slug' => $_slug,
        ]);
		}
    }
}
