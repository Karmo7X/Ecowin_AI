<?php

namespace App\Filament\Resources;

use App\Filament\Resources\QuestionResource\Pages;
use App\Filament\Resources\QuestionResource\RelationManagers;
use App\Models\Question;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;



class QuestionResource extends Resource
{
    protected static ?string $model = Question::class;

    protected static ?string $navigationIcon = 'heroicon-o-question-mark-circle';
    protected static ?int $navigationSort = 3;
    protected static ?string $navigationGroup = 'Content';
    protected static ?string $navigationLabel = 'Questions';
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
                    // Forms\Components\TextInput::make("question_ar")->maxValue(255)->required(),
                    Forms\Components\TextInput::make("question_en")->maxValue(255)->required(),
                    // Forms\Components\TextInput::make("answer_ar")->maxValue(255)->required(),
                    Forms\Components\TextInput::make("answer_en")->maxValue(255)->required(),
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Tables\Columns\TextColumn::make("question_ar")->searchable()->sortable(),
                Tables\Columns\TextColumn::make("question_en")->searchable()->sortable(),
                // Tables\Columns\TextColumn::make("answer_ar")->sortable(),
                Tables\Columns\TextColumn::make("answer_en")->sortable(),
            ])
            ->filters([
                // يمكن إضافة فلترة هنا إذا لزم الأمر
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
            // يمكن إضافة علاقات هنا لاحقًا
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListQuestions::route('/'),
            'create' => Pages\CreateQuestion::route('/create'),
            'edit' => Pages\EditQuestion::route('/{record}/edit'),
        ];
    }
}
