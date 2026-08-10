<?php
namespace App\Services;

class LyricsBlockParser
{
    public static function parse(string $content): array
    {
        if (empty(trim($content))) {
            return [];
        }

        $content = self::cleanContent($content);

        $content = preg_replace('/<(strong|b)>(.*?)<\/(strong|b)>/i', '**$2**', $content);
        $content = preg_replace('/<(em|i)>(.*?)<\/(em|i)>/i', '_$2_', $content);
        $content = preg_replace('/<br\s*\/?>/i', "\n", $content);

        $paragraphs = preg_split('/<\/?p>/i', $content);

        if (count($paragraphs) <= 1) {
            $paragraphs = explode("\n\n", $content);
        }

        return collect($paragraphs)
            ->map(fn($p) => trim(strip_tags($p)))
            ->filter(fn($p) => !empty($p))
            ->values()
            ->map(function ($blockContent, $index) {
                return [
                    'id' => time() + $index,
                    'type' => self::detectBlockType($blockContent, $index),
                    'content' => $blockContent,
                    'label' => self::generateBlockLabel($blockContent, $index),
                ];
            })
            ->toArray();
    }

    private static function cleanContent(string $html): string
    {
        $html = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $html);
        $html = preg_replace('/<style\b[^>]*>(.*?)<\/style>/is', '', $html);
        $html = html_entity_decode($html, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        return $html;
    }

    public static function detectBlockType(string $content, int $index = 0): string
    {
        $content = strtolower($content);

        $chorusPatterns = [
            '/^coro/i',
            '/\(.*?x\s*\d+.*?\)/i',
            '/^\[coro\]/i',
            '/^estribillo/i',
            '/aleluya/i',
            '/gloria/i',
            '/hosanna/i',
        ];

        foreach ($chorusPatterns as $pattern) {
            if (preg_match($pattern, $content)) {
                return 'chorus';
            }
        }

        $bridgePatterns = [
            '/^puente/i',
            '/^\[puente\]/i',
            '/^bridge/i',
        ];

        foreach ($bridgePatterns as $pattern) {
            if (preg_match($pattern, $content)) {
                return 'bridge';
            }
        }

        return 'verse';
    }

    public static function generateBlockLabel(string $content, int $index): string
    {
        $type = self::detectBlockType($content, $index);

        return match ($type) {
            'chorus' => 'Coro',
            'bridge' => 'Puente',
            default => 'Estrofa ' . ($index + 1),
        };
    }
}