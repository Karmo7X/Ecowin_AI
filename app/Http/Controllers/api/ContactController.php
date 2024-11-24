<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\Request;


class ContactController extends Controller
{
    //store for contact message
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'subject' => 'required|string|max:255',
            ]);

            $contact = new Contact();
            $contact->name = $request->input('name');
            $contact->email = $request->input('email');
            $contact->subject = $request->input('subject');
            $contact->save();

            return response()->json([
                'status' => 200,
                'message' => 'Contact added successfully',
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $exception) {
            // Return validation errors in an array
            return response()->json([
                'status' => 422,
                'errors' => $exception->errors(),
            ], 422);
        } catch (\Exception $exception) {
            // Handle any other exceptions
            return response()->json([
                'status' => 500,
                'message' => $exception->getMessage(),
            ], 500);
        }
    }

}
