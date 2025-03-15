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

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart'; // أيقونة عربة التسوق تناسب المنتجات
    protected static ?int $navigationSort = 1;
    protected static ?string $navigationGroup = 'Store'; // نفس المجموعة لتجميع المنتجات والفئات معًا
    protected static ?string $navigationLabel = 'Products'; // تصحيح التسمية لتكون بصيغة الجمع
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
                    Forms\Components\TextInput::make("name_ar")->maxValue(50)->required(),
                    Forms\Components\TextInput::make("name_en")->maxValue(50)->required(),
                    Forms\Components\Select::make("category_id")
                        ->relationship('category', "name_ar")->label("category ar"),
                    Forms\Components\Select::make("category_id")
                        ->relationship('category', "name_en")->label("category en"),
                    Forms\Components\TextInput::make("price")->numeric()->required(),
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make("image"),
                Tables\Columns\TextColumn::make("name_ar")->searchable()->sortable(),
                Tables\Columns\TextColumn::make("name_en")->searchable()->sortable(),
                Tables\Columns\TextColumn::make("category.name_ar")->searchable()->sortable()->toggleable(),
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
