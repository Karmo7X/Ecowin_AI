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
     protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['name_ar'] = $data['name_en']; 
        return $data;
    }
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index'); 
    }
}
