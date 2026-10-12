<?php

namespace App\Enums;

enum LiturgicalSeason: string
{
    case Adviento  = 'adviento';
    case Navidad   = 'navidad';
    case Cuaresma  = 'cuaresma';
    case Pascua    = 'pascua';
    case Ordinario = 'ordinario';
    case General   = 'general';


    public function label(): string
    {
        return match ($this){
            self::Adviento => 'Adviento',
            self::Navidad => 'Navidad',
            self::Cuaresma => 'Cuaresma',
            self::Pascua => 'Pascua',
            self::Ordinario => 'Tiempo Ordinario',
            self::General => 'Uso general',
        };
    }

    public function color(): string
    {
        return match ($this){
            self::Adviento => '#6A4C93',
            self::Navidad => '#C9A227',
            self::Cuaresma => '#4A235A',
            self::Pascua => '#E0B43A',
            self::Ordinario => '#2E7D32',
            self::General => '#78909C',
        };
    }

    public static function options(): array
    {
        return array_map(fn (self $s) => [
            'value' => $s->value,
            'label' => $s->label(),
            'color' => $s->color(),
        ], self::cases());
    }

}