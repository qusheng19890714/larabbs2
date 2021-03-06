<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use Illuminate\Http\Request;
use App\Models\User;
use App\Handlers\ImageUploaderHandler;
use Intervention\Image\Image;

class UsersController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth', ['except'=>['show']]);
    }

    /**
     * 个人中心
     * @param User $user
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }

    /**
     * 编辑页面
     * @param User $user
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(User $user)
    {
        $this->authorize('update', $user);
        return view('users.edit', compact('user'));
    }

    public function update(UserRequest $request, User $user, ImageUploaderHandler $uploader)
    {
        $this->authorize('update', $user);

        $data = $request->all();

        if ($request->avatar) {

            $result = $uploader->save($request->avatar, 'avatar', $user->id, 362);

            if ($result) {

                $data['avatar']  = $result['path'];
            }

        }

        $user->update($data);

        return redirect()->route('users.show', $user->id)->with('success', '个人资料更新成功');
    }
}
