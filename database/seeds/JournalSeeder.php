<?php

use App\Client;
use App\Journal;
use Illuminate\Database\Seeder;

class JournalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $clients = Client::all();

        foreach ($clients as $client) {
            $numberOfJournals = rand(1, 5);

            Journal::factory($numberOfJournals)->belongsToClient($client)->create();
        }
    }
}
