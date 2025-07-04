<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Cache;


class EditProduct extends EditRecord
{
    protected static string $resource = ProductResource::class;
    protected function afterSave(): void
{
    Cache::flush(); //  Cache::forget('products_...')
}

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
