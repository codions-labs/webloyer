<?php

declare(strict_types=1);

namespace Webloyer\Infra\Framework\Laravel\Database\Seeds;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Kodeine\Acl\Models\Eloquent\Role;

class RolesTableSeeder extends Seeder
{
    public function run()
    {
        DB::transaction(function () {
            if (Role::count() > 0) {
                return;
            }

            Role::create([
                'name'        => 'Administrator',
                'slug'        => 'administrator',
                'description' => 'Manage administration privileges.',
            ]);

            Role::create([
                'name'        => 'Developer',
                'slug'        => 'developer',
                'description' => 'Manage developer privileges.',
            ]);

            Role::create([
                'name'        => 'Operator',
                'slug'        => 'operator',
                'description' => 'Manage operator privileges.',
            ]);
        });
    }
}
