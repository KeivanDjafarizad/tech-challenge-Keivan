<?php

namespace App\Http\Controllers;

use App\Client;
use App\Journal;
use Illuminate\Http\Request;

class JournalsController extends Controller
{
    public function destroy($client, $journal)
    {
        $journal = Journal::where('id', $journal)
            ->whereHas('client', function ($query) use ($client) {
                $query
                    ->where('id', $client)
                    ->where('user_id', auth()->id());
            })
            ->firstOrFail();

        $journal->delete();

        return response()->json(['message' => 'Journal deleted successfully'], 204);
    }

    public function store($client, Request $request)
    {
        $validated = $request->validate([
            'notes' => 'required|string',
            'entry_date' => 'required|date',
        ]);

        $client = Client::where('id', $client)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $journal = new Journal();
        $journal->notes = $request->input('notes');
        $journal->entry_date = $request->input('entry_date');

        $client->journals()->save($journal);

        return response()->json(['message' => 'Journal created successfully', 'journal' => $journal], 201);
    }
}
