<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $manageUser = new Permission();
        $manageUser->name = 'Manage users';
        $manageUser->slug = 'manage-users';
        $manageUser->save();

        $createLeave = new Permission();
        $createLeave->name = 'Create Leave';
        $createLeave->slug = 'create-leave';
        $createLeave->save();

        $deleteLeave = new Permission();
        $deleteLeave->name = 'Delete Leave';
        $deleteLeave->slug = 'delete-leave';
        $deleteLeave->save();

        $approveLeave = new Permission();
        $approveLeave->name = 'Approve Leave';
        $approveLeave->slug = 'approve-leave';
        $approveLeave->save();
    }
}
