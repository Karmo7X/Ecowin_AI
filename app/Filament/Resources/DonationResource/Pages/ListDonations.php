<?php

namespace App\Filament\Resources\DonationResource\Pages;

use App\Filament\Resources\DonationResource;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use App\Enums\DonationStatusEnum;
use App\Models\Donation;
use Illuminate\Support\Collection; // Make sure Collection is imported if used in `getTabs` or other methods

class ListDonations extends ListRecords
{
    protected static string $resource = DonationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    /**
     * Define tabs for filtering donations based on user role.
     * Admin users see no tabs, displaying the full table.
     * Agent users see specific tabs for their assigned donations.
     */
    public function getTabs(): array
    {
        $userRole = auth()->user()->role;
        $tabs = [];

        // If the user is an admin, return an empty array of tabs.
        // Filament will then display the default full table for admins.
        if ($userRole === "admin") {
            return [];
        }
        // If the user is an agent, define specific tabs for them.
        elseif ($userRole === "agent") {
            $agent = auth()->user()->agent ?? null;

            // If the current user is an agent but doesn't have an agent record, show no tabs.
            if (!$agent) {
                return [];
            }

            $tabs["available"] = Tab::make("Available for Pickup")
                ->badge(
                    Donation::where("status", DonationStatusEnum::PENDING->value)
                        ->whereNull('agent_id') // Donations not yet assigned to any agent
                        ->count()
                )
                ->modifyQueryUsing(
                    fn($query) =>
                    $query->where("status", DonationStatusEnum::PENDING->value)
                        ->whereNull('agent_id')
                );

            $tabs["my_donations"] = Tab::make("My Pending Donations")
                ->badge(
                    Donation::where('status', DonationStatusEnum::PENDING->value)
                        ->whereHas('agent', fn($q) => $q->where('user_id', auth()->id())) // Donations assigned to current agent and still pending
                        ->count()
                )
                ->modifyQueryUsing(
                    fn($query) =>
                    $query->where('status', DonationStatusEnum::PENDING->value)
                        ->whereHas('agent', fn($query) => $query->where('user_id', auth()->id()))
                );

            $tabs["completed_by_me"] = Tab::make("Completed by Me")
                ->badge(
                    Donation::where("status", DonationStatusEnum::COMPLETED->value)
                        ->whereHas('agent', fn($q) => $q->where('user_id', auth()->id())) // Donations completed by current agent
                        ->count()
                )
                ->modifyQueryUsing(
                    fn($query) =>
                    $query->where("status", DonationStatusEnum::COMPLETED->value)
                        ->whereHas('agent', fn($query) => $query->where('user_id', auth()->id()))
                );

            $tabs["cancelled_by_me"] = Tab::make("Cancelled by Me")
                ->badge(
                    Donation::where("status", DonationStatusEnum::CANCELLED->value)
                        ->whereHas('agent', fn($q) => $q->where('user_id', auth()->id())) // Donations cancelled by current agent
                        ->count()
                )
                ->modifyQueryUsing(
                    fn($query) =>
                    $query->where("status", DonationStatusEnum::CANCELLED->value)
                        ->whereHas('agent', fn($query) => $query->where('user_id', auth()->id()))
                );
        }

        return $tabs;
    }
}
