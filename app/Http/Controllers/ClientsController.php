<?php

namespace App\Http\Controllers;

use App\Client;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ClientsController extends Controller
{
    public function index()
    {
        $clients = Client::where('user_id', auth()->id())->get();

        foreach ($clients as $client) {
            $client->append('bookings_count');
        }

        return view('clients.index', ['clients' => $clients]);
    }

    public function create()
    {
        return view('clients.create');
    }

    public function show($client)
    {
        $client = Client::where('id', $client)
            ->where('user_id', auth()->id())
            ->with('bookings')
            ->firstOrFail();

        return view('clients.show', ['client' => $client]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'nullable|string|max:190',
            'email' => [
                Rule::requiredIf(empty($request->phone)),
                'nullable',
                'email',
            ],
            'phone' => [
                Rule::requiredIf(empty($request->email)),
                'nullable',
                'string',
                'regex:/^[0-9+\s]*$/'
            ],
        ], [
            'email.required' => 'The email or phone field is required.',
            'phone.required' => 'The email or phone field is required.',
            'phone.regex' => 'The phone field must be a valid phone number and contain only numbers, spaces and + sign.',
        ]);

        $client = new Client;
        $client->name = $request->get('name');
        $client->email = $request->get('email');
        $client->phone = $request->get('phone');
        $client->address = $request->get('address');
        $client->city = $request->get('city');
        $client->postcode = $request->get('postcode');

        auth()->user()->clients()->save($client);

        return $client;
    }

    public function destroy($client)
    {
        $client = Client::where('id', $client)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $client->delete();

        return response()->json(['message' => 'Client deleted'], 204);
    }
}
