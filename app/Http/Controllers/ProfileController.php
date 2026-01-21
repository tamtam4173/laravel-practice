<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * プロフィール編集画面
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * プロフィール更新
     *
     * - 更新可能項目を user_name / email に限定
     * - email 変更時は email_verified_at をリセット
     * - ProfileUpdateRequest に user_name ルールが無くてもここで補完
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        // 既存のバリデーション結果（通常は email 等）
        $data = $request->validated();

        // user_name が送られていれば軽いバリデーションを追加（任意文字列255まで）
        if ($request->has('user_name')) {
            $request->validate([
                'user_name' => ['nullable', 'string', 'max:255'],
            ]);
            $data['user_name'] = $request->input('user_name');
        }

        // 許可フィールドのみ反映（想定外のキーを排除）
        $allowKeys = ['user_name', 'email'];
        $filtered = array_intersect_key($data, array_flip($allowKeys));

        $user = $request->user();
        $user->fill($filtered);

        // email が変わったら認証フラグをリセット
        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * アカウント削除
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current-password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
