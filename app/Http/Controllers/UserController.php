<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Group;
use App\Models\GroupMember;
use App\Models\Permission;

class UserController extends Controller
{
    public function get_all_users() {
        return User::all();
    }

    public function get_user(Request $request, User $user) {
        return User::where('id',$user->id)->with('pivot_groups')->first();
    }

    public function add_user(Request $request) {
        $user = new User($request->all());
        $user->save();
        return $user;
    }

    public function update_user(Request $request, User $user) {
        $user->update($request->all());
        return $user;
    }

    public function delete_user(Request $request, User $user) {
        GroupMember::where('user_id',$user->id)->delete();
        $user->delete();
        return "1";
    }

    public function login_user(Request $request, User $user) {
        Auth::login($user,true);
        return "1";
    }

    public function search($search_string='') {
        $search_elements_parsed = preg_split('/[\s,]+/',strtolower($search_string));
        $search = []; $users = [];
        if (count($search_elements_parsed) === 1 && $search_elements_parsed[0]!='') {
            $search[0] = $search_elements_parsed[0];
            $users = User::select('id','unique_id','first_name','last_name','email')
                ->where(function ($query) use ($search) {
                    $query->where('unique_id',$search[0])
                        ->orWhere('id',$search[0])
                        ->orWhere('first_name','like',$search[0].'%')
                        ->orWhere('last_name','like',$search[0].'%')
                        ->orWhere('email','like',$search[0].'%');
                })->orderBy('first_name', 'asc')->orderBy('last_name', 'asc')
                    ->limit(25)->get()->toArray();
        } else if (count($search_elements_parsed) > 1) {
            $search[0] = $search_elements_parsed[0];
            $search[1] = $search_elements_parsed[count($search_elements_parsed)-1];
            $users = User::select('id','unique_id','first_name','last_name','email')
                ->where(function ($query) use ($search) {
                    $query->where(function ($query) use ($search) {
                        $query->where('first_name','like',$search[0].'%')
                            ->where('last_name','like',$search[1].'%');
                    })->orWhere(function ($query) use ($search) {
                        $query->where('first_name','like',$search[1].'%')
                            ->where('last_name','like',$search[0].'%');
                    });
                })->orderBy('first_name', 'asc')->orderBy('last_name', 'asc')
                    ->limit(25)->get()->toArray();
        }
        foreach($users as $index => $user) {
            $users[$index] = array_intersect_key($user, array_flip(['id','unique_id','first_name','last_name','email']));
        }

        return $users;
    }

    public function set_permissions(Request $request, User $user) {
        $request->validate([
            'permissions' => 'array',
        ]);
        Permission::where('user_id',$user->id)->delete();
        foreach($request->permissions as $permission) {
            $permission = new Permission([
                'user_id' =>$user->id,
                'permission' => $permission
            ]);
            $permission->save();
        }
        return $request->permissions;
    }
    public function get_permissions(Request $request, User $user) {
        return $user->user_permissions;
    }

}