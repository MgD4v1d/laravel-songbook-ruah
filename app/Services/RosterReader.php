<?php 

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class RosterReader
{

    public function read(string $path, string $mimeType): array
    {
       $response = Http::timeout(90)
            ->withHeaders(['x-goog-api-key' => config('services.gemini.key')])
            ->post($this->endpoint(), [
                'contents' => [[
                    'parts' => [
                        ['inline_data' => [
                            'mime_type' => $mimeType,
                            'data'      => base64_encode(file_get_contents($path)),
                        ]],
                        ['text' => $this->prompt()],
                    ],
                ]],
                'generationConfig' => [
                    'temperature'      => 0,
                    'responseMimeType' => 'application/json',
                    'responseSchema'   => $this->schema(),
                ],
            ]);


        if ($response->failed()) {
            report(new RuntimeException('Gemini: ' . $response->body()));
            throw new RuntimeException('No se pudo leer el rol. Intenta de nuevo.');
        }

        $json = $response->json('candidates.0.content.parts.0.text');
        $data = json_decode($json ?? '', true);

        if (! is_array($data) || ! isset($data['month'], $data['entries'])) {
            throw new RuntimeException('La respuesta de Gemini no tiene el formato esperado.');
        }

        return $this->normalize($data);
    }

    private function endpoint(): string
    {
        $model = config('services.gemini.model');
        return "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent";
    }

    

    private function prompt(): string
    {
        return <<<PROMPT
        Este documento es el rol mensual de coros de una parroquia católica en México.
        Extrae TODAS las misas de la tabla, de todos los coros.

        Reglas:
        - "month": el mes y año del encabezado del documento, formato YYYY-MM.
        - "date": fecha completa YYYY-MM-DD. Usa el año del encabezado; pero si el rol incluye días del mes siguiente (por ejemplo, el 1 de enero en el rol de diciembre), usa el año que corresponde a ese día.
        - "time": hora en formato de 24 horas HH:MM (ej. 7:00 pm → 19:00, 9:30 → 09:30).
        - Si una fecha tiene varios horarios, crea UNA entrada por cada horario.
        - "choir": el nombre del coro tal como aparece (ej. "RUAH", "Santa María").
        - "celebration": la celebración especial si la hay (ej. "XV años", "Bautizos"); si está vacía, null.
        - No inventes filas. Si un dato no se puede leer, omite esa fila.
        PROMPT;
    }

    private function schema(): array
    {
        return [
            'type' => 'OBJECT',
            'properties' => [
                'month'   => ['type' => 'STRING'],
                'entries' => [
                    'type'  => 'ARRAY',
                    'items' => [
                        'type' => 'OBJECT',
                        'properties' => [
                            'date'        => ['type' => 'STRING'],
                            'time'        => ['type' => 'STRING'],
                            'choir'       => ['type' => 'STRING'],
                            'celebration' => ['type' => 'STRING', 'nullable' => true],
                        ],
                        'required' => ['date', 'time', 'choir'],
                    ],
                ],
            ],
            'required' => ['month', 'entries'],
        ];
    }

    private function normalize(array $data): array
    {
        $month = $data['month'];
        $valid = [];

        try{
            $start = Carbon::createFromFormat('Y-m-d', $month . '-01')->startOfDay();
        } catch (\Throwable){
            throw new RuntimeException('No se pudo identificar el mes del rol.');
        }

        $from = $start->toDateString();
        $to = $start->copy()->endOfMonth()->addDays(7)->toDateString();

        foreach($data['entries'] as $entry){
            // 1 date
            try{
                $parsed = Carbon::createFromFormat('Y-m-d', $entry['date']);
            }catch(\Throwable){
                continue;
            }

            // Rol de diciembre: si Gemini puso enero con el año del encabezado, corregir el año
            if ($start->month === 12 && $parsed->month === 1 && $parsed->year === $start->year) {
                $parsed->addYear();
            }

            $date = $parsed->format('Y-m-d');

            if ($date < $from || $date > $to) {
                continue;
            }

            // 2) Hora
            if (! preg_match('/^(\d{1,2}):(\d{2})$/', trim($entry['time']), $m)) {
                continue;
            }

            $hour   = (int) $m[1];
            $minute = (int) $m[2];

            if ($hour > 23 || $minute > 59) {
                continue;
            }

            $time = sprintf('%02d:%02d', $hour, $minute);

            // 3) Coro
            $choir = trim($entry['choir'] ?? '');

            if($choir === ''){
                continue;
            }

            // 4) Celebración
            $celebration = trim($entry['celebration'] ?? '') ?: null;

            $valid[] = [
                'date'        => $date,
                'time'        => $time,
                'choir'       => $choir,
                'celebration' => $celebration,
            ];

        }

        usort($valid, fn ($a, $b) => ($a['date'] . $a['time']) <=> ($b['date'] . $b['time']));

        return ['month' => $month, 'entries' => $valid];
    }

}
