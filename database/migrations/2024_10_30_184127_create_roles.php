<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\permission;
use App\Models\user;


return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $role1 = Role::create (['name' => 'admin']);
        $role2 = Role::create (['name' => 'usuario']);
        $user = User::find(1);
        $user->assignRole($role1);

        $role1 = Role::create (['name' => 'admin']);
        $role2 = Role::create (['name' => 'usuario']);
        $user = User::find(2);
        $user->assignRole($role1);

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    //
    }
};
