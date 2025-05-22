<?php

namespace Database\Seeders;

use App\Models\Account_transaction;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AccountTransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Account_transaction::factory()->count(100)->create();
    }
}
