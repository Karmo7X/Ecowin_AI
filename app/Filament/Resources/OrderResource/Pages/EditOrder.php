<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Models\Transaction;
use App\Models\User;

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
    $user = User::find($data['user_id']);

    if (!$user || !$user->wallet) {
        return $data; // no wallet found, skip
    }

    // إذا الحالة تغيرت إلى completed لأول مرة
    if ($data['status'] === 'completed' && $oldStatus !== 'completed') {
        // سجل معاملة
        Transaction::create([
            'user_id' => $data['user_id'],
            'type' => 'credit',
            'amount' => (int) $data['points'],
        ]);

        // أضف النقاط إلى المحفظة
        $user->wallet->increment('points', $data['points']);
    }

    // إذا الحالة كانت completed وتم تغييرها لأي حالة تانية
    elseif ($oldStatus === 'completed' && $data['status'] !== 'completed') {
        // سجل معاملة خصم
        Transaction::create([
            'user_id' => $data['user_id'],
            'type' => 'debit',
            'amount' => (int) $data['points'],
        ]);

        // خصم النقاط من المحفظة
        $user->wallet->decrement('points', $data['points']);
    }

    return $data;
}
}
