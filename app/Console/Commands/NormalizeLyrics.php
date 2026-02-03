<?php

namespace App\Console\Commands;

use App\Models\Song;
use Illuminate\Console\Command;

class NormalizeLyrics extends Command
{
    protected $signature = 'songs:normalize-lyrics';

    protected $description = 'Normaliza las letras para que cada estrofa sea un párrafo HTML';

    public function handle(): int
    {
        $songs = Song::whereNotNull('lyrics')->where('lyrics', '!=', '')->get();

        $this->info("Normalizando {$songs->count()} canciones...");

        $updated = 0;

        foreach ($songs as $song) {
            $normalized = $this->normalizeLyricsHtml($song->lyrics);

            if ($normalized !== $song->lyrics) {
                $song->update(['lyrics' => $normalized]);
                $updated++;
                $this->line("  ✓ {$song->title}");
            }
        }

        $this->info("Listo. {$updated} canciones normalizadas.");

        return Command::SUCCESS;
    }

    private function normalizeLyricsHtml(string $html): string
    {
        // 1. Extraer contenido de todos los <p>
        preg_match_all('/<p[^>]*>(.*?)<\/p>/s', $html, $matches);

        if (empty($matches[1])) {
            return $html;
        }

        // 2. Juntar todo el contenido con <br/>
        $parts = array_filter(array_map('trim', $matches[1]), fn ($s) => $s !== '');
        $combined = implode('<br/>', $parts);

        // 3. Normalizar variantes de <br> a <br/>
        $normalized = preg_replace('/<br\s*\/?>/', '<br/>', $combined);

        // 4. Separar estrofas por doble <br/> o mas
        $stanzas = preg_split('/(<br\/>){2,}/', $normalized);

        // 5. Limpiar cada estrofa
        $stanzas = array_filter(array_map(function ($s) {
            $s = trim($s);
            $s = preg_replace('/^(<br\/>)+/', '', $s);
            $s = preg_replace('/(<br\/>)+$/', '', $s);

            return trim($s);
        }, $stanzas), fn ($s) => $s !== '' && $s !== '<br/>');

        return implode("\n\n", array_map(fn ($s) => "<p>{$s}</p>", $stanzas));
    }
}
