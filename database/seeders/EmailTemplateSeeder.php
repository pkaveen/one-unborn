<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\EmailTemplate; 

class EmailTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         EmailTemplate::updateOrCreate(
            ['name' => 'User Created', 'company_id' => 0],
            [
                'subject' => 'Welcome {name} to {company_name}',
                'body' => 'Hello {name},<br><br>Welcome to {company_name}!<br>Your account has been created with the email: {email}.<br>Joining Date: {joining_date}<br><br>Thank you,<br>Team {company_name}',
                'status' => 'Active',
            ]
        );
    }
}
