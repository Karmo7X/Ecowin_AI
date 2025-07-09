<?php

namespace App\Filament\Resources;

use App\Enums\DonationStatusEnum;
use App\Enums\TransactionTypeEnum; // Ensure this Enum is imported if used
use App\Filament\Resources\DonationResource\Pages;
use App\Models\Donation;
use App\Models\Address;
use App\Models\Agent;
use App\Models\Transaction; // Ensure this Model is imported if used
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class DonationResource extends Resource
{
    protected static ?string $model = Donation::class;
    protected static ?string $navigationIcon = 'heroicon-o-gift';
    protected static ?string $navigationGroup = 'Donations Management'; // Changed to English
    protected static ?string $modelLabel = 'Donation'; // Changed to English
    protected static ?string $pluralModelLabel = 'Donations'; // Changed to English

    /**
     * Get the navigation badge for the resource.
     * Only visible to admins.
     */
    public static function getNavigationBadge(): ?string
    {
        if (!auth()->check()) {
            return null;
        }
        if (auth()->user()->role !== 'admin') {
            return null;
        }
        return static::getModel()::count();
    }

    /**
     * Determine if the user can edit a record.
     * Only admins can edit donations.
     */
    public static function canEdit(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return auth()->user()->role === "admin";
    }

    /**
     * Define the form schema for creating and editing donations.
     */
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Donation Details')->schema([ // Changed to English
                    Forms\Components\Select::make("user_id")
                        ->relationship('user', "name")
                        ->label("Donor") // Changed to English
                        ->required()
                        ->searchable()
                        ->preload(),

                    Forms\Components\Select::make("address_id")
                        ->label("Pickup Address") // Changed to English
                        ->options(
                            Address::all()
                                ->mapWithKeys(function ($address) {
                                    return [
                                        $address->id =>
                                        'City: ' . $address->city . // Changed to English
                                            ', Governorate: ' . $address->governate . // Changed to English
                                            ', Street: ' . $address->street . // Changed to English
                                            ($address->building_no ? ', Building No: ' . $address->building_no : '') // Changed to English
                                    ];
                                })
                                ->toArray()
                        )
                        ->required()
                        ->placeholder('Select a pickup address'), // Changed to English

                    Forms\Components\TextInput::make('pieces')
                        ->label('Number of Pieces') // Changed to English
                        ->numeric()
                        ->minValue(0)
                        ->nullable(),

                    Forms\Components\Textarea::make('description')
                        ->label('Description') // Changed to English
                        ->maxLength(65535)
                        ->columnSpanFull()
                        ->nullable(),

                    Forms\Components\Select::make("status")
                        ->options(DonationStatusEnum::class)
                        ->label('Status') // Changed to English
                        ->required()
                        ->default(DonationStatusEnum::PENDING->value),

                    Forms\Components\Select::make('agent_id')
                        ->relationship('agent.user', 'name')
                        ->label('Assigned Agent') // Changed to English
                        ->placeholder('No agent assigned') // Changed to English
                        ->nullable()
                        ->searchable()
                        ->preload()
                        ->visible(auth()->user()->role === 'admin'),
                ])->columns(2),
            ]);
    }

    /**
     * Define the table columns and actions for displaying donations.
     */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make("user.name")
                    ->label("Donor") // Changed to English
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status') // Changed to English
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        DonationStatusEnum::PENDING->value => 'warning',
                        DonationStatusEnum::COMPLETED->value => 'success',
                        DonationStatusEnum::CANCELLED->value => 'danger',
                        default => 'gray', // Default color for unexpected values
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make("pieces")
                    ->label("Number of Pieces") // Changed to English
                    ->numeric()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make("description")
                    ->label('Description') // Changed to English
                    ->limit(50)
                    ->tooltip(fn(Donation $record): string => $record->description ?: 'No description') // Changed to English
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make("address.city")
                    ->label('Pickup Address Details') // Changed to English
                    ->description(function (Donation $record) {
                        if ($record->address) {
                            return 'City: ' . $record->address->city . // Changed to English
                                ', Governorate: ' . $record->address->governate . // Changed to English
                                ', Street: ' . $record->address->street . // Changed to English
                                ($record->address->building_no ? ', Building No: ' . $record->address->building_no : ''); // Changed to English
                        }
                        return 'No address'; // Changed to English
                    })
                    ->searchable([
                        'address.city',
                        'address.governate',
                        'address.street',
                        'address.building_no'
                    ])
                    ->sortable('address.city'),
                Tables\Columns\TextColumn::make('agent.user.name')
                    ->label('Assigned Agent Name') // Changed to English
                    ->placeholder('No agent assigned') // Changed to English
                    ->sortable()
                    ->searchable()
                    ->visible(auth()->user()->role === 'admin'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creation Date') // Changed to English
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Filter by Status') // Changed to English
                    ->options(DonationStatusEnum::class)
                    ->attribute('status'),

                Tables\Filters\SelectFilter::make('user_id')
                    ->relationship('user', 'name')
                    ->label('Filter by Donor') // Changed to English
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('agent_id')
                    ->relationship('agent.user', 'name')
                    ->label('Filter by Assigned Agent') // Changed to English
                    ->searchable()
                    ->preload()
                    ->visible(auth()->user()->role === 'admin'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\DeleteAction::make(),

                Tables\Actions\Action::make("assign_agent")
                    ->label("Assign Agent") // Changed to English
                    ->icon("heroicon-o-user-plus")
                    ->color("primary")
                    ->form([
                        Forms\Components\Select::make("agent_id")
                            ->label("Select Agent") // Changed to English
                            ->options(
                                Agent::with('user')->get()->pluck('user.name', 'id')->toArray()
                            )
                            ->required(),
                    ])
                    ->action(function (Donation $donation, $data) {
                        $donation->agent_id = $data["agent_id"];
                        $donation->save();
                        Notification::make()
                            ->title("Agent assigned successfully.") // Changed to English
                            ->success()
                            ->send();
                    })
                    ->visible(
                        fn(Donation $donation) =>
                        auth()->user()->role === "admin" &&
                            is_null($donation->agent_id)
                    ),

                Tables\Actions\Action::make("receive_donation")
                    ->label("Receive Donation") // Changed to English
                    ->icon("heroicon-m-hand-raised")
                    ->color("success")
                    ->action(function (Donation $donation) {
                        $userId = auth()->id();
                        $agent = Agent::where("user_id", $userId)->first();

                        if (!$agent) {
                            Notification::make()
                                ->title("Error: Agent not found for current user.") // Changed to English
                                ->danger()
                                ->send();
                            return;
                        }

                        $donation->agent_id = $agent->id;
                        // The donation remains 'pending' until truly completed.
                        $donation->save();
                        Notification::make()
                            ->title("Donation received (status remains pending).") // Changed to English
                            ->success()
                            ->send();
                    })
                    ->visible(
                        fn(Donation $donation) => (auth()->user()->role === "agent" &&
                            $donation->status === DonationStatusEnum::PENDING->value &&
                            is_null($donation->agent_id)) // Agent can receive unassigned pending donations

                    ),

                Tables\Actions\Action::make("cancel_donation")
                    ->label("Cancel Donation") // Changed to English
                    ->icon("heroicon-m-x-circle")
                    ->color("danger")
                    ->action(function (Donation $donation) {
                        $donation->status = DonationStatusEnum::CANCELLED->value;
                        $donation->save();
                        Notification::make()->title("Donation cancelled.")->success()->send(); // Changed to English
                    })
                    ->visible(
                        fn(Donation $donation) =>
                        $donation->status === DonationStatusEnum::PENDING->value &&
                            (auth()->user()->role === "admin" || optional($donation->agent)->user_id === auth()->id())
                    ),

                Tables\Actions\Action::make("complete_donation")
                    ->label("Complete Donation") // Changed to English
                    ->icon("heroicon-o-check-circle")
                    ->color("success")
                    ->action(function (Donation $donation) {
                        $donation->status = DonationStatusEnum::COMPLETED->value;
                        $donation->save();

                        // Create a transaction with zero points, as requested
                        Transaction::create([
                            'user_id' => $donation->user_id,
                            'amount' => 0, // Zero points
                            'type' => TransactionTypeEnum::CREDIT->value,
                            'description' => 'Donation completed (no points awarded)', // Changed to English
                        ]);

                        Notification::make()->title("Donation completed.")->success()->send(); // Changed to English
                    })
                    ->visible(
                        fn(Donation $donation) =>
                        $donation->status === DonationStatusEnum::PENDING->value &&
                            optional($donation->agent)->user_id === auth()->id()
                    ),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    /**
     * Define the resource's relation managers.
     */
    public static function getRelations(): array
    {
        return [
            // No RelationManagers as requested
        ];
    }

    /**
     * Define the resource's pages.
     */
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDonations::route('/'),
            'create' => Pages\CreateDonation::route('/create'),
            'edit' => Pages\EditDonation::route('/{record}/edit'),
        ];
    }
}
