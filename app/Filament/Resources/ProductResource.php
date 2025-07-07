<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    protected static ?int $navigationSort = 1;
    protected static ?string $navigationGroup = 'Store';
    protected static ?string $navigationLabel = 'Products';
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

                Forms\Components\Section::make()->schema([
                    Forms\Components\FileUpload::make("image")->directory("products")->image()->imageEditor()->required(),
                    Forms\Components\Hidden::make("name_ar"),
                    Forms\Components\TextInput::make("name_en")->label("Name")->maxValue(50)->required(),
                    Forms\Components\Select::make("category_id")
                        ->relationship('category', "name_en")->label("Category"),
                    Forms\Components\TextInput::make("price")->numeric()->required()->minvalue(0),
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make("image"),
                Tables\Columns\TextColumn::make("name_en")->label("Name")->searchable()->sortable(),
                Tables\Columns\TextColumn::make("category.name_en")->searchable()->sortable()->toggleable(),
                Tables\Columns\TextColumn::make("price")->sortable()->toggleable(),
                //
            ])
            ->filters([
                Tables\Filters\SelectFilter::make("category")->relationship("category", "name_ar"),
                Tables\Filters\SelectFilter::make("category")->relationship("category", "name_en")

                //
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
