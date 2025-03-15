<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CouponResource\Pages;
use App\Filament\Resources\CouponResource\RelationManagers;
use App\Models\Coupon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Models\User;
use Illuminate\Support\Str;
use Filament\Tables\Grouping\Group;


class CouponResource extends Resource
{
    protected static ?string $model = Coupon::class;

    protected static ?string $navigationIcon = 'heroicon-o-ticket';
    protected static ?int $navigationSort = 3;

    protected static ?string $navigationGroup = 'Coupons Management';
    protected static ?string $navigationLabel = 'Coupons';
    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->role === "admin"; //هام جدا في اخفاء الريسورس عن الايجنت role
    }
    public static function canAccess(): bool
    {
        return auth()->user()?->role === "admin"; //هام جدا في اخفاء الريسورس عن الايجنت role
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make("code")
                    ->unique(ignoreRecord: true)
                    ->default(Str::upper(Str::random(8)))
                    ->disabled()
                    ->dehydrated()
                    ->required(),
                Forms\Components\TextInput::make("discount_value")
                    ->label("Discount (%)")
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(100)
                    ->required()
                    ->suffix('%')
                    ->default(0),

                Forms\Components\TextInput::make("price")
                    ->numeric()
                    ->required(),

                Forms\Components\Select::make("brand_id")
                    ->relationship('brand', "name_ar")->label("brand ar"),
                Forms\Components\Select::make("brand_id")
                    ->relationship('brand', "name_en")->label("brand en"),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            // ->query(fn(Builder $query) => $query->whereNull('user_id'))
            ->columns([
                Tables\Columns\TextColumn::make("code")->searchable()->sortable(),
                Tables\Columns\TextColumn::make("discount_value")
                    ->label("Discount (%)")
                    ->formatStateUsing(fn($state) => $state . '%')
                    ->sortable(),
                Tables\Columns\TextColumn::make("price")->sortable(),
                Tables\Columns\TextColumn::make("brand.name_ar")->searchable()->sortable()->toggleable(),
                Tables\Columns\TextColumn::make("brand.name_en")->searchable()->sortable()->toggleable(),
                // Tables\Columns\TextColumn::make("user.name")->searchable()->sortable()->toggleable()
                //     ->placeholder('without user'),
                // Tables\Columns\TextColumn::make('user.name')
                //     ->hidden(! auth()->user()->isAdmin())
            ])
            ->filters([
                Tables\Filters\Filter::make('Without User')
                    ->query(fn(Builder $query) => $query->whereNull('user_id'))
                    ->default(),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ])
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCoupons::route('/'),
            'create' => Pages\CreateCoupon::route('/create'),
            'edit' => Pages\EditCoupon::route('/{record}/edit'),
        ];
    }
}
