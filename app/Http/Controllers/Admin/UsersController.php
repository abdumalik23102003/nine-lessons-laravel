<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Users\UserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UsersController extends Controller
{
    public function index(Request $request): View
    {
        $query = User::query()->orderByDesc('id');

        if ($value = $request->string('id')->toString()) {
            $query->where('id', $value);
        }

        if ($value = $request->string('name')->toString()) {
            $query->where('name', 'like', '%' . $value . '%');
        }

        if ($value = $request->string('email')->toString()) {
            $query->where('email', 'like', '%' . $value . '%');
        }

        if ($value = $request->string('role')->toString()) {
            $query->where('role', $value);
        }

        return view('admin.users.index', [
            'users' => $query->paginate(20)->withQueryString(),
            'roles' => User::rolesList(),
        ]);
    }

    public function edit(User $user): View
    {
        return view('admin.users.edit', [
            'user' => $user,
            'roles' => User::rolesList(),
            'canChangeRole' => request()->user()->isAdmin() && request()->user()->isNot($user),
        ]);
    }

    public function update(UserRequest $request, User $user): RedirectResponse
    {
        $data = $request->safe()->only(['name', 'email']);

        if ($request->user()->isAdmin() && request()->user()->isNot($user)) {
            $data['role'] = $request->validated('role');
        }
        $user->update($data);
        return redirect()->route('admin.users.index')->with('status', "Foydalanuvchi yangilandi.");
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        abort_if($request->user()->is($user), 403, "O'zingizni o'chira olmaysiz. прикинь :)");
        abort_unless($request->user()->isAdmin(), 403);
        $user->delete();

        return redirect()->route('admin.users.index')->with('status', "Foydalanuvchi o`chirildi.");
    }
}
