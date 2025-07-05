<?php

namespace App\Filament\Resources\CouponResource\Pages;

use App\Filament\Resources\CouponResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Log;

class CreateCoupon extends CreateRecord
{
    protected static string $resource = CouponResource::class;
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index'); 
    }
}
