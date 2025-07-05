<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Models\Transaction;
class EditOrder extends EditRecord
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
      protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index'); 
    }
     protected function mutateFormDataBeforeSave(array $data): array
    {
        $oldStatus = $this->record->status;

        // لو الحالة تغيرت إلى completed لأول مرة
        if ($data['status'] === 'completed' && $oldStatus !== 'completed') {
            Transaction::create([
                'user_id' => $data['user_id'],
                'type' => 'credit',
                'amount' => (int) $data['points'],
            ]);
        }

        return $data;
    }
    
}
