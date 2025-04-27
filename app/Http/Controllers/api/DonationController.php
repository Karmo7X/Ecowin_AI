<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDonationRequest;
use App\Models\Donation;
use App\Models\Address;
use App\Models\DonationImage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DonationController extends Controller
{
    public function store(StoreDonationRequest $donationRequest)
    {
        $user = Auth::user();

        DB::beginTransaction();

        try {
            // 1. حفظ العنوان
            $address = Address::create([
                'governate' => $donationRequest->governate,
                'city' => $donationRequest->city,
                'street' => $donationRequest->street,
                'user_id' => $user->id,
            ]);

            // 2. حفظ التبرع
            $donation = Donation::create([
                'user_id' => $user->id,
                'address_id' => $address->id,
                'pieces' => $donationRequest->pieces,
                'description' => $donationRequest->description,
            ]);

            // 3. رفع الصور وتخزينها
            if ($donationRequest->hasFile('images')) {
                foreach ($donationRequest->file('images') as $imageFile) {
                    $path = $imageFile->store('donations', 'public');

                    DonationImage::create([
                        'donation_id' => $donation->id,
                        'image' => $path,
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'message' => 'شكرا لتبرعك ',
                'donation' => $donation->load('images', 'address'),
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'حدث خطأ أثناء تسجيل التبرع',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
