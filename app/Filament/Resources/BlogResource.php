<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BlogResource\Pages;
use App\Filament\Resources\BlogResource\RelationManagers;
use App\Models\Blog;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BlogResource extends Resource
{
    protected static ?string $model = Blog::class;

    protected static ?string $navigationIcon = 'heroicon-o-newspaper'; // أيقونة الصحيفة تناسب المقالات
    protected static ?int $navigationSort = 3;
    protected static ?string $navigationGroup = 'Content'; // فصل المدونة في مجموعة أخرى للمحتوى
    protected static ?string $navigationLabel = 'Blogs'; // التسمية ثابتة

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
                // Forms\Components\TextInput::make('title_ar')
                //     ->label('Title ar')
                //     ->required()
                //     ->maxLength(255),
                Forms\Components\TextInput::make('title_en')
                    ->label('Title en')
                    ->required()
                    ->maxLength(255),

                // Forms\Components\Textarea::make('body_ar')
                //     ->label('Content ar')
                //     ->required(),
                Forms\Components\Textarea::make('body_en')
                    ->label('Content en')
                    ->required(),

                Forms\Components\FileUpload::make('image')
                    ->label('Image')
                    ->image()->imageEditor()
                    ->directory('blogs')
                    ->visibility('public')
                    ->nullable(),
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Tables\Columns\TextColumn::make('title_ar')->label('Title ar')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('title_en')->label('Title en')->sortable()->searchable(),
                // Tables\Columns\TextColumn::make('body_ar')->label('Content ar')->limit(50),
                Tables\Columns\TextColumn::make('body_en')->label('Content en')->limit(50),
                Tables\Columns\ImageColumn::make('image'),
                Tables\Columns\TextColumn::make('created_at')->label('Created At')->date(),
                //
            ])
            ->filters([

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
            'index' => Pages\ListBlogs::route('/'),
            'create' => Pages\CreateBlog::route('/create'),
            'edit' => Pages\EditBlog::route('/{record}/edit'),
        ];
    }
}
