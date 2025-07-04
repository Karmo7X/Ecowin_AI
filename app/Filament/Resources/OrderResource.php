<?php

namespace App\Filament\Resources;

use App\Enums\OrderStatusEnum;
use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers;
use App\Models\Order;
use App\Models\Agent;
use App\Models\Transaction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Enums\OrderStatusEnum as EnumsOrderStatusEnum;
use App\Enums\TransactionTypeEnum as EnumsTransactionTypeEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Actions\Action as ActionsAction;
use Filament\Notifications\Notification;
use Filament\Facades\Filament;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    // public static function shouldRegisterNavigation(): bool
    // {
    //     return auth()->user()?->role === "agent"; //هام جدا في اخفاء الريسورس عن الايجنت role
    // }
    // public static function canAccess(): bool
    // {
    //     return auth()->user()?->role === "agent"; //هام جدا في اخفاء الريسورس عن الايجنت role
    // }


    // public static function canEdit($record): bool
    // {
    //     return !auth()->user()->role === "admin"; // السماح بالتعديل فقط لغير الـ Admin
    // }
    public static function getNavigationBadge(): ?string
    {
        if (!auth()->check()) {
            return null; // إذا لم يكن هناك مستخدم مسجل، لا تُرجع أي قيمة
        }

        if (auth()->user()->role !== 'admin') {
            return null; // منع المستخدم غير المسؤول من رؤية الشارة
        }

        return static::getModel()::count();
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Forms\Components\Section::make()->schema([
                    Forms\Components\Select::make("user_id")
                        ->relationship('user', "name")->label("user"),
                    Forms\Components\Select::make("status")->options([
                        "pending" => EnumsOrderStatusEnum::PENDING->value,
                        "completed" => EnumsOrderStatusEnum::COMPLETED->value,
                        "cancelled" => EnumsOrderStatusEnum::CANCELLED->value,
                    ])->required()->default("pending"),
                    Forms\Components\Select::make("address_id")
                        ->relationship('address', "city")->label("city")->required(),
                    Forms\Components\TextInput::make("points")->numeric()->required()->minvalue(0),
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make("user.name")->searchable()->sortable()->toggleable(),
                Tables\Columns\TextColumn::make('status')->label('Status')->sortable(),
                Tables\Columns\TextColumn::make("points")->sortable()->toggleable(),
                Tables\Columns\TextColumn::make("address.city")->searchable()->sortable()->toggleable(),
                Tables\Columns\TextColumn::make('agent.user.name') // جلب اسم الوكيل المرتبط
                    ->label('Agent Name')
                    ->sortable()
                    ->searchable()
                    ->visible(auth()->user()->role === 'admin')
                    ->placeholder('No assigned agent'),


            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\Action::make("Receive Order") // Changed action name
                    ->icon("heroicon-m-check")
                    ->color("success")
                    ->action(function (Order $order) {
                        $userId = auth()->id();
                        $agent = Agent::where("user_id", $userId)->first();

                        if (!$agent) {
                            Notification::make()
                                ->title("Error: Agent not found for current user.")
                                ->danger()
                                ->send();
                            return; // Stop execution if agent not found
                        }

                        $order->agent_id = $agent->id;
                        $order->save();
                        Notification::make()
                            ->title("Order received") // Changed notification title
                            ->success()
                            ->send();
                    })
                    ->visible(
                        fn(Order $order) =>
                        $order->status === OrderStatusEnum::PENDING->value &&
                            auth()->user()->role === "agent" &&
                            $order->agent_id == null
                    ),
                Tables\Actions\Action::make("Cancel Order")
                    ->icon("heroicon-m-trash")
                    ->color("danger")
                    ->action(function (Order $order) {
                        $order->status = OrderStatusEnum::CANCELLED->value;
                        $order->save();
                        Notification::make()->title("Order Cancelled")->success()->send();
                    })->visible(
                        fn(Order $order) =>
                        $order->status === OrderStatusEnum::PENDING->value &&
                            optional($order->agent)->user_id === auth()->id()
                    ),
                Tables\Actions\Action::make("Complete Order") // Changed action name
                    ->icon("heroicon-o-check")
                    ->color("success")
                    ->action(function (Order $order) {
                        $order->status = OrderStatusEnum::COMPLETED->value;
                        $order->save();

                        Transaction::create([
                            'user_id' => $order->user_id,
                            'amount' => $order->points,
                            'type' => EnumsTransactionTypeEnum::CREDIT->value,
                        ]);

                        Notification::make()->title("Order completed")->success()->send();
                    })->visible(
                        fn(Order $order) =>
                        $order->status === OrderStatusEnum::PENDING->value &&
                            optional($order->agent)->user_id === auth()->id()
                    ),
                Tables\Actions\Action::make("Edit Points") // Changed action name
                    ->icon("heroicon-m-pencil-square")
                    ->form([
                        Forms\Components\TextInput::make("points")
                            ->numeric()
                            ->default(fn(Order $order) => $order->points)
                            ->minValue(0)
                            ->required(),
                    ])
                    ->action(function (Order $order, $data) {
                        $order->points = $data["points"];
                        $order->save();
                        Notification::make()->title("Points Updated")->success()->send();
                    })->visible(
                        fn(Order $order) =>
                        $order->status === OrderStatusEnum::PENDING->value &&
                            optional($order->agent)->user_id === auth()->id()
                    ),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
