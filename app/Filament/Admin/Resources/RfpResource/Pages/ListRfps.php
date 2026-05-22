<?php

namespace App\Filament\Admin\Resources\RfpResource\Pages;

use App\Filament\Admin\Resources\RfpResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRfps extends ListRecords
{
    protected static string $resource = RfpResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
