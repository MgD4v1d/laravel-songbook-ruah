<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Member;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AdminMemberController extends Controller
{
    public function index(): Response
    {
        $members = Member::orderBy('name')->get();

        return Inertia::render('Admin/Members/Index', [
            'members' => $members,
        ]);
    }

    public function store(Request $request)
    {

        $request->merge(['email' => strtolower(trim($request->input('email', '')))]);

        $data = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:255|unique:members,email', 
        ]);

        $member = Member::create($data);

        return back()->with('success', "Integrante '{$member->name}' agregado exitosamente");

    }


    public function update(Request $request, Member $member)
    {
        $member->update($request->validate(['active' => 'required|boolean']));
        return back();
    }

    public function destroy(Member $member)
    {
        $member->delete();
        return back()->with('success', "Integrante eliminado exitosamente");
    }

}
