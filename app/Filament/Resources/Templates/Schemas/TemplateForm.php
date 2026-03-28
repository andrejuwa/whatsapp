<?php

namespace App\Filament\Resources\Templates\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TemplateForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->columnSpan(2)
                    ->schema([
                        TextInput::make('nome')
                            ->required(),

                        Textarea::make('texto')
                            ->default(null)
                            ->columnSpanFull(),

                        TextInput::make('media_id')
                            ->default(null),

                        TextInput::make('wa_id')
                            ->default(null),

                        TextInput::make('tipo')
                            ->required(),

                        Toggle::make('ativo')
                            ->required(),
                    ])
            ]);
    }
}
