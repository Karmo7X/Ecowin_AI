<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Cache;


class CreateProduct extends CreateRecord
{
    protected static string $resource = ProductResource::class;
    protected function afterCreate(): void
    {
        Cache::flush(); // أو Cache::forget('products_...')
    }
}
