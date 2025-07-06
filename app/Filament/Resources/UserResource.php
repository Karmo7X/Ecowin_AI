<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Enums\UserRoleEnum as EnumsUserRoleEnum;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

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
                //

                Forms\Components\TextInput::make('name')
                    ->label('Name')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('email')
                    ->label('Email')
                    ->required()
                    ->email()
                    ->unique(User::class, 'email', ignoreRecord: true),

                Forms\Components\TextInput::make('password')
                    ->label('Password')
                    ->password()
                    ->revealable() // <--- الإضافة الأولى: أيقونة العين
                    ->required(fn(string $context): bool => $context === 'create') // <--- الإضافة الثانية: مطلوب فقط عند الإنشاء
                    ->dehydrateStateUsing(fn($state) => Hash::make($state))
                    ->dehydrated(fn($state) => filled($state)), // <--- الإضافة الثالثة: لا يحفظ الهاش إذا كان الحقل فارغاً في التعديل


                Forms\Components\Select::make("role")->options([
                    "user" => EnumsUserRoleEnum::USER->value,
                    "agent" => EnumsUserRoleEnum::AGENT->value,
                    "admin" => EnumsUserRoleEnum::ADMIN->value,

                ])->required()->default("user")->reactive(),

                Forms\Components\TextInput::make('assigned_area')
                    ->label('Assigned Area')
                    ->placeholder('Enter the assigned area for this agent') // تغيير النص التوضيحي
                    // سيظهر هذا الحقل فقط إذا كانت قيمة 'role' هي 'agent'
                    ->hidden(fn(Forms\Get $get): bool => $get('role') !== 'agent')
                    // سيصبح هذا الحقل مطلوبًا فقط إذا كانت قيمة 'role' هي 'agent'
                    ->required(fn(Forms\Get $get): bool => $get('role') === 'agent'),




            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Name')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('email')->label('Email')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('role')->label('Role')->sortable(),
                Tables\Columns\TextColumn::make('created_at')->label('Created At')->date(),
                //
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
