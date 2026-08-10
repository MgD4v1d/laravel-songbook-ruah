<?php
namespace App\Services;
use Illuminate\Validation\ValidationException;

class TelegramSongTextParser
{
    public static function parse(string $text): array
    {
        $text = trim(str_replace("\r\n", "\n", $text));

        [$header, $body] = self::splitHeaderAndBody($text);

        $title    = self::extractField($header, 'T[ií]tulo');
        $artist   = self::extractField($header, 'Artista');
        $category = self::extractField($header, 'Categor[ií]a');

        if (! $title) {
            throw ValidationException::withMessages([
                'text' => ['No se encontró la línea "Título:" en el mensaje.'],
            ]);
        }

        if (trim($body) === '') {
            throw ValidationException::withMessages([
                'text' => ['No se encontró la letra de la canción después de los datos.'],
            ]);
        }

        return [
            'title' => $title,
            'artist' => $artist,
            'category' => $category,
            'lyrics' => trim($body),
        ];
    }


    private static function splitHeaderAndBody(string $text): array
    {
        $lines = explode("\n", $text);
        $headerLines = [];
        $bodyStart = null;

        foreach ($lines as $i => $line) {
            if (preg_match('/^\s*(T[ií]tulo|Artista|Categor[ií]a)\s*:/iu', $line)) {
                $headerLines[] = $line;
                continue;
            }

            $bodyStart = (trim($line) === '' && $headerLines) ? $i + 1 : $i;
            break;
        }

        return [
            implode("\n", $headerLines),
            $bodyStart !== null ? implode("\n", array_slice($lines, $bodyStart)) : '',
        ];
    }
    
    private static function extractField(string $header, string $labelPattern): ?string
    {
        if (preg_match('/^\s*' . $labelPattern . '\s*:\s*(.+)$/mu', $header, $m)) {
            $value = trim($m[1]);
            return $value !== '' ? $value : null;
        }

        return null;
    }
}