<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Song;
use Illuminate\Support\Str;

class AIController extends Controller
{
    public function generateRepertoireAi(Request $request): JsonResponse
    {
        $request->validate([
            'celebration_type' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        $songs = Song::all(['id','title', 'artist', 'lyrics'])
            ->map(fn($s) => "{$s->id}|{$s->title}|{$s->artist}|" . Str::limit(preg_replace('/\s+/', ' ', trim($s->lyrics)), 150))
            ->implode("\n");

        $prompt = $this->generatePrompt($request->celebration_type, $request->notes, $songs);

        try {
            $response = $this->callApiGpt($prompt);
            return response()->json($response);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    private function generatePrompt(string $type, ?string $notes, string $songs): string 
    {
        $textNotes = $notes ? "\nConsideraciones adicionales: {$notes}" : "";

        return <<<PROMPT
        Eres un experto en liturgia católica y música sacra con profundo conocimiento del calendario litúrgico romano.
        Tu tarea es seleccionar un repertorio de cantos para una misa a partir del catálogo proporcionado.

        ## CONTEXTO LITÚRGICO
        TIPO DE CELEBRACIÓN: {$type}
        {$textNotes}

        ## CATÁLOGO DISPONIBLE
        Formato: id|título|artista|momento_litúrgico_sugerido|fragmento_de_letra
        {$songs}

        ## ESTRUCTURA DE LA MISA Y CRITERIOS DE SELECCIÓN

        ### 1. ENTRADA
        - **Propósito**: Recibir a la asamblea, establecer el tono litúrgico
        - **Características**: Festivo, acorde al tiempo litúrgico (Adviento, Navidad, Cuaresma, Pascua, Tiempo Ordinario)
        - **Temática**: Venida del Señor, llamado a la celebración, alegría del encuentro

        ### 2. ACTO PENITENCIAL (Señor, ten piedad / Kyrie)
        - **Propósito**: Preparación espiritual, arrepentimiento
        - **Cuándo omitir**: NUNCA se omite, pero puede ser breve si la entrada ya lo incluye
        - **Nota**: Si el catálogo no tiene Kyrie específico, puede usarse un canto de piedad general

        ### 3. GLORIA
        - **Propósito**: Himno de alabanza trinitaria
        - **REGLA ESTRICTA**: Se OMITE en:
            - Adviento (espera silenciosa)
            - Cuaresma (ayuno y conversión)
            - Misas de difuntos (respeto y luto)
            - Misas de Cuaresma y Adviente específicamente
        - **Cuándo cantar**: Navidad, Pascua, solemnidades, fiestas, domingos de Tiempo Ordinario

        ### 4. ACLAMACIÓN ANTES DEL EVANGELIO
        - **Propósito**: Preparar la escucha del Evangelio
        - **Cuaresma**: Sustituir "Aleluya" por "Honor y Gloria a ti, Señor Jesús" o similar
        - **Adviento**: Puede usarse "Aleluya" o una aclamación apropiada al tiempo

        ### 5. OFERTORIO
        - **Propósito**: Presentación de pan, vino y ofrendas
        - **Temática**: Unidad, sacrificio, gratitud, ofrenda de sí mismo
        - **Carácter**: Meditativo pero con movimiento procesional si aplica

        ### 6. SANTO (Santo, Santo, Santo)
        - **Propósito**: Aclamación eucarística, unión con los ángeles
        - **Nota**: Debe ser un "Santo" explícito, no cualquier canto de adoración

        ### 7. CORDERO DE DIOS (Agnus Dei)
        - **Propósito**: Preparación para la comunión, pedido de misericordia
        - **Estructura**: "Cordero de Dios que quitas el pecado del mundo..."
        - **Final**: "Danos la paz" o "Ten piedad de nosotros"

        ### 8. COMUNIÓN
        - **Propósito**: Acción de gracias eucarística, comunión espiritual
        - **Duración**: Debe cubrir toda la distribución de la comunión
        - **Temática**: Presencia real, unión con Cristo, fraternidad

        ### 9. SALIDA (Despedida)
        - **Propósito**: Envío a la misión, conclusión celebrativa
        - **Temática**: Anuncio del Evangelio, alegría misionera, advocación mariana (opcional)
        - **Carácter**: Enérgico, con sentido de "ir en paz"

        ## REGLAS ABSOLUTAS
        1. **IDs válidos**: Usa ÚNICAMENTE IDs que existan en el catálogo proporcionado
        2. **Sin repeticiones**: Ninguna canción puede repetirse en dos momentos distintos
        3. **Coherencia temática**: El repertorio debe tener unidad temática según el tiempo litúrgico
        4. **Momento apropiado**: Respeta la función de cada momento (no pongas un Gloria en el lugar del Santo)
        5. **Null permitido**: Si no hay canción adecuada para un momento específico, usa `null` en `id` y `titulo`

        ## FORMATO DE RESPUESTA
        Responde ÚNICAMENTE con un JSON válido, sin markdown, sin texto adicional, sin comentarios:

        {
            "repertorio": {
                "entrada": {"id": number|null, "titulo": string|null, "razon": "Explicación litúrgica breve"},
                "piedad": {"id": number|null, "titulo": string|null, "razon": "Explicación litúrgica breve"},
                "gloria": {"id": number|null, "titulo": string|null, "razon": "Explicación litúrgica breve o 'Omitido por ser [tiempo litúrgico]'"},
                "aclamacion": {"id": number|null, "titulo": string|null, "razon": "Explicación litúrgica breve"},
                "ofertorio": {"id": number|null, "titulo": string|null, "razon": "Explicación litúrgica breve"},
                "santo": {"id": number|null, "titulo": string|null, "razon": "Explicación litúrgica breve"},
                "cordero": {"id": number|null, "titulo": string|null, "razon": "Explicación litúrgica breve"},
                "comunion": {"id": number|null, "titulo": string|null, "razon": "Explicación litúrgica breve"},
                "salida": {"id": number|null, "titulo": string|null, "razon": "Explicación litúrgica breve"}
            },
            "notas_generales": "Observaciones sobre coherencia temática, tiempo litúrgico, sugerencias de interpretación musical, o advertencias si el catálogo es limitado"
        }
        PROMPT;
    }

    private function callApiGemini(string $prompt): array 
    {
        $response = Http::withHeaders([
            'x-goog-api-key' => env('GEMINI_API_KEY'),
            'Content-Type' => 'application/json',
        ])->post('https://generativelanguage.googleapis.com/v1beta/models/gemini-3-flash-preview:generateContent', [
            'contents' => [
                [
                    'parts' => [
                        [
                            'text' => $prompt,
                        ],
                    ],
                ],
            ],
        ]);

        if ($response->failed()) {
            throw new \Exception('Error al conectar con la API de Gemini: ' . $response->body());
        }

        $data = $response->json('candidates.0.content.parts.0.text');

        //Limpiar y parsear JSON
         $json = preg_replace('/```json\s*|\s*```/', '', $data);
        
        return json_decode($json, true) ?? ['error' => 'No se pudo parsear la respuesta'];
    }

    private function callApiGpt(string $prompt): array 
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . env('OPENAI_API_KEY'),
            'Content-Type' => 'application/json',
        ])->post('https://api.openai.com/v1/responses', [
            'model' => 'gpt-4.1',
            'input' => $prompt,
            'text' => [
                'format' => ['type' => 'json_object'],
            ],
        ]);

        if ($response->failed()) {
            throw new \Exception('Error al conectar con la API de OpenAI: ' . $response->body());
        }

       $data = $response->json('output.0.content.0.text');

        //Limpiar y parsear JSON
         $json = preg_replace('/```json\s*|\s*```/', '', $data);
        
        return json_decode($json, true) ?? ['error' => 'No se pudo parsear la respuesta'];
    }
}
