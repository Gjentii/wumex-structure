<?php

namespace App\Filament\Resources\SinglePasswordResource\Pages;

use App\Filament\Resources\SinglePasswordResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSinglePasswords extends ListRecords
{
    protected static string $resource = SinglePasswordResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
