<?php

namespace App\Console\Commands;

use App\Models\Song;
use Illuminate\Console\Command;

class MigrateSongsToBlocks extends Command
{
    protected $signature = 'songs:migrate-to-blocks {--force : Forzar migración incluso si ya tienen bloques}';
    protected $description = 'Migra las letras HTML existentes al formato de bloques';


    public function handle()
    {
        $this->info('Migrando canciones HTML a formato de bloques...');
        
        $force = $this->option('force');
        
        $query = Song::query();
        
        if (!$force) {
            $query->where(function($q) {
                $q->whereNull('lyrics_blocks')
                  ->orWhereRaw('JSON_LENGTH(lyrics_blocks) = 0');
            });
        }
        
        $total = $query->count();
        $this->info("Total de canciones a migrar: {$total}");
        
        $bar = $this->output->createProgressBar($total);
        $bar->start();

        $migrated = 0;
        $errors = 0;

        $query->chunk(50, function ($songs) use ($bar, &$migrated, &$errors) {
            foreach ($songs as $song) {
                try {
                    if (!empty($song->lyrics)) {
                        // El accessor convertirá automáticamente HTML a bloques
                        $blocks = $song->lyrics_blocks;
                        
                        if (count($blocks) > 0) {
                            $song->updateQuietly([
                                'lyrics_blocks' => $blocks
                            ]);
                            $migrated++;
                        }
                    }
                } catch (\Exception $e) {
                    $errors++;
                    $this->newLine();
                    $this->error("Error en '{$song->title}': {$e->getMessage()}");
                }
                
                $bar->advance();
            }
        });

        $bar->finish();
        $this->newLine(2);
        
        $this->info("✅ Migración completada");
        $this->info("   Migradas: {$migrated}");
        
        if ($errors > 0) {
            $this->warn("   Errores: {$errors}");
        }
    }

}