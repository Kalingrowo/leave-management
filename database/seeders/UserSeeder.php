<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Hash;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admin = Role::where('slug', 'admin')->first();
        $developer = Role::where('slug', 'developer')->first();
        $manager = Role::where('slug', 'manager')->first();

        $manageUsers = Permission::where('slug', 'manage-users')->first();
        $createLeave = Permission::where('slug', 'create-leave')->first();
        $deleteLeave = Permission::where('slug', 'delete-leave')->first();
        $approveLeave = Permission::where('slug', 'approve-leave')->first();

        $user1 = new User();
        $user1->name = 'Admin';
        $user1->email = 'admin@arsoft.co-id';
        $user1->password = Hash::make('123456');
        $user1->save();
        $user1->roles()->attach($admin);
        $user1->permissions()->attach($manageUsers);
        $user1->permissions()->attach($approveLeave);
        $user1->permissions()->attach($deleteLeave);

        $user2 = new User();
        $user2->name = 'Developer';
        $user2->email = 'developer@arsoft.co.id';
        $user2->password = Hash::make('123456');
        $user2->save();
        $user2->roles()->attach($developer);
        $user2->permissions()->attach($createLeave);

        $user3 = new User();
        $user3->name = 'Manager';
        $user3->email = 'manager@arsoft.co.id';
        $user3->password = Hash::make('123456');
        $user3->save();
        $user3->roles()->attach($manager);
        $user3->permissions()->attach($createLeave);
    }
}
