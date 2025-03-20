<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use Filament\Resources\Components\Tab;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Enums\OrderStatusEnum as EnumsOrderStatusEnum;
use Illuminate\Support\Collection;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\RedirectResponse;

// use Closure;


class ListOrders extends ListRecords
{
    public Collection $ordersByStatuses;
    public function __construct()
    {
        $this->ordersByStatuses = collect([
            'pending' => Order::where("status", EnumsOrderStatusEnum::PENDING->value)
                ->where(function ($q) {
                    $q
                        ->orWhereDoesntHave('agent');
                })->count(),
            'my_orders' => Order::where("status", EnumsOrderStatusEnum::PENDING->value)
                ->whereHas('agent', fn($q) => $q->where('user_id', auth()->id()))->count(),
            'completed' => Order::where("status", EnumsOrderStatusEnum::COMPLETED->value)
                ->whereHas('agent', fn($q) => $q->where('user_id', auth()->id()))->count(),
            'cancelled' => Order::where("status", EnumsOrderStatusEnum::CANCELLED->value)
                ->whereHas('agent', fn($q) => $q->where('user_id', auth()->id()))->count(),


        ]);
    }
    protected static string $resource = OrderResource::class;
    public function getTabs(): array
    {
        $userRole = auth()->user()->role; // الحصول على دور المستخدم
        $tabs = [];

        if ($userRole === "agent") {
            $tabs["pending"] = Tab::make("Pending Orders")
                ->badge($this->ordersByStatuses[EnumsOrderStatusEnum::PENDING->value] ?? "0")
                ->modifyQueryUsing(
                    fn($query) =>
                    $query->where("status", "pending")
                        ->where(function ($q) {
                            $q->orWhereDoesntHave('agent'); // الطلبات التي ليس لديها Agent
                        })
                );

            $tabs["my orders"] = Tab::make("My Orders")
                ->badge($this->ordersByStatuses['my_orders'] ?? "0")
                ->modifyQueryUsing(
                    fn($query) =>
                    $query->where("status", "pending")
                        ->whereHas('agent', fn($query) => $query->where('user_id', auth()->id()))
                );

            $tabs["completed"] = Tab::make("Completed")
                ->badge($this->ordersByStatuses[EnumsOrderStatusEnum::COMPLETED->value] ?? "0")
                ->modifyQueryUsing(
                    fn($query) =>
                    $query->where("status", "completed")
                        ->whereHas('agent', fn($query) => $query->where('user_id', auth()->id()))
                );

            $tabs["cancelled"] = Tab::make("Cancelled")
                ->badge($this->ordersByStatuses[EnumsOrderStatusEnum::CANCELLED->value] ?? "0")
                ->modifyQueryUsing(
                    fn($query) =>
                    $query->where("status", "cancelled")
                        ->whereHas('agent', fn($query) => $query->where('user_id', auth()->id()))
                );
        }

        return $tabs;
    }


    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
