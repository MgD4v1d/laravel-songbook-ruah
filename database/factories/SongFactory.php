<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Song>
 */
class SongFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $songs = [
            ['title' => 'Santo, Santo, Santo', 'artist' => 'Tradicional'],
            ['title' => 'Reunidos Señor', 'artist' => 'Dones & Música para Edificar'],
            ['title' => 'Pan del Cielo', 'artist' => 'Eleazar Cortés'],
            ['title' => 'A tu presencia Señor', 'artist' => 'Jessed'],
            ['title' => 'Cordero de Dios - Ministerio Kerygma', 'artist' => 'Dones & Música para Edificar'],
            ['title' => 'A todo pulmón', 'artist' => 'Los Padrecitos'],
            ['title' => 'Señor ten piedad (6/8)', 'artist' => 'Cefurmus Scra'],
            ['title' => 'Hemos venido a adorarte', 'artist' => 'Marco Lopez'],
            ['title' => 'Hoy se enciende una llama', 'artist' => 'Aciprensa'],
            ['title' => 'Que llegue pronto tu reino', 'artist' => 'Rafael Moreno'],
        ];

        $song = $this->faker->randomElement($songs);


        return [
            'title' => $song['title'],
            'artist' => $song['artist'],
            'key' => $this->faker->randomElement(['C -2', 'G -4', 'Am', 'F', 'D', 'Em', 'B', 'A -1', 'E +2']),
            'rhythm' => $this->faker->randomElement(['70sDisco1', ' Jive', '80sDiscoBeat', 'RoseDisco', 'BoleroLento', 'PopBossa', '60sPianoPop', '90sGuitarPop']),
            'tempo' => $this->faker->randomElement(['91', '130', '75', '120', '80', '60']),
            'lyrics' => $this->generateLyrics(),
            'video_url' => $this->faker->boolean(30) ? 'https://www.youtube.com/watch?v=' . $this->faker->lexify('???????????') : null
        ];
    }


    private function generateLyrics(): string
    {
        return "## Verso 1\n" . 
               "Santo, santo, santo es el Señor\n" .
               "Santo, santo, Dios del universo\n\n" .
               "## Coro\n" .
               "Llenos están el cielo y la tierra\n" .
               "de tu gloria, Señor\n\n" .
               "## Verso 2\n" .
               "Hosanna en el cielo\n" .
               "Bendito el que viene\n" .
               "en nombre del Señor";
    }
}
