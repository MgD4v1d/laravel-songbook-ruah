<?php

namespace Database\Seeders;

use App\Models\Song;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SongSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Song::create([
            'title' => 'Gloria (San juan pablo II)',
            'artist' => 'San juan pablo II / Pbro. Alvaro Carrilo',
            'key' => 'C -4',
            'lyrics' => "**Gloria , Gloria, a Dios en el Cielo**  
                        **y en la tierra paz a los hombres**  
                        **que Ama el Señor (2 veces)**

                        Te alabamos, Señor te bendecimos  
                        Te adoramos, Te glorificamos.

                        **Gloria , Gloria, a Dios en el Cielo**  
                        **y en la tierra paz a los hombres**  
                        **que Ama el Señor.**

                        Tu eres el Cordero que quitas el pecado,  
                        Ten piedad de nosotros y escucha nuestra  
                        Oración.

                        **Gloria , Gloria, a Dios en el Cielo**  
                        **y en la tierra paz a los hombres**  
                        **que Ama el Señor.**

                        Solo tu eres Santo  
                        Solo tu altisimo  
                        con el espíritu Santo  
                        en la gloria de Dios Padre.

                        **Gloria , Gloria, a Dios en el Cielo**  
                        **y en la tierra paz,**  
                        **a los hombres que Ama el Señor,**  
                        **a los hombres que Ama el Señor.**",
            'rhythm' => 'CanadianRock',
            'tempo' => '130',
            'video_url' => ''

        ]);


        Song::factory()->count(10)->create();
    }
}
