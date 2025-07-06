<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use App\Models\Agent;
use App\Models\User;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        // قم بإنشاء سجل المستخدم الجديد
        $user = parent::handleRecordCreation($data);

        // إذا كان دور المستخدم 'agent'، قم بإنشاء سجل وكيل (Agent) جديد وربطه بالمستخدم
        if ($user->role === 'agent') {
            Agent::create([
                'user_id' => $user->id,
                'assigned_area' => $data['assigned_area'] ?? null,
            ]);

            // قم بتحديث كائن المستخدم لضمان تحميل أحدث البيانات من قاعدة البيانات.
            // هذا مفيد إذا كنت تخطط لاستخدام الكائن لاحقًا في هذا الطلب.
            $user->refresh();
        }

        // أعد كائن المستخدم الذي تم إنشاؤه
        return $user;
    }
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
