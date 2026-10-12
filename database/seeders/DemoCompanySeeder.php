<?php

namespace Database\Seeders;

use App\Enums\CompanyRole;
use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DemoCompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (! app()->environment('local', 'testing')) {
            return;
        }

        DB::transaction(callback: function (): void {
            $atlas = Company::firstOrCreate(
                ['slug' => 'atlas-digital'],
                ['name' => 'Atlas']
            );

            $horizonte = Company::firstOrCreate(
                ['slug' => 'horizonte-studio'],
                ['name' => 'Horizonte Studio']
            );

            $ana = $this->createDemoUser(
                'Ana Martins',
                'ana@example.com'
            );

            $miguel = $this->createDemoUser(
                'Miguel Santos',
                'miguel@example.com'
            );

            $alona = $this->createDemoUser(
                'Alona Costa',
                'alona.costa2@gmail.com'
            );

            $atlas->users()->syncWithoutDetaching([
                $ana->id => ['role' => CompanyRole::Owner->value],
                $miguel->id => ['role' => CompanyRole::Admin->value],
                $alona->id => ['role' => CompanyRole::Member->value],
            ]);

            $horizonte->users()->syncWithoutDetaching([
                $alona->id => ['role' => CompanyRole::Owner->value],
                $ana->id => ['role' => CompanyRole::Member->value],
            ]);
        });
    }

    private function createDemoUser(string $name, string $email): User
    {
        $user = User::where('email', $email)->first();

        if ($user !== null) {
            return $user;
        }

        return User::factory()->create([
            'name' => $name,
            'email' => $email,
        ]);
    }
}
