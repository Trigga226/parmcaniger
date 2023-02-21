<?php

namespace App\Http\Controllers;

use App\Http\Resources\User\UserResource;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Intervention\Image\Facades\Image;

class ProfileController extends Controller
{
    /**
     * Edit user profile.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit()
    {
        $title = 'Mon profil';

        return view('profile.index',[
            'title' => $title,
            'user' => new UserResource(auth()->user('user'))
        ]);
    }

    /**
     * Update user profile.
     *
     * @param Request $req
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Request $req)
    {
        $user = auth()->user('user');

        // Update information
        if ($req->step == 1) return $this->updateInformation($req, $user);

        // Update avatar
        if ($req->step == 2) return $this->updateAvatar($req, $user);

        // Update email
        if ($req->step == 3) return $this->updateEmail($req,$user);

        // Update password
        if ($req->step == 4) return $this->updatePassword($req, $user);
    }

    /**
     * Update user information.
     *
     * @param $req
     * @param $user
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    private function updateInformation($req, $user)
    {
        // Validate information
        $this->validateInformation($req, $user->id);

        // Update information
        $user->fill(['name' => $req->name, 'phone' => $req->phone])->save();

        // Return ok
        return response()->json(['status' => 'ok', 'user' => new UserResource($user->fresh())],200);
    }

    /**
     * Update user avatar.
     *
     * @param $req
     * @param $user
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    private function updateAvatar($req, $user)
    {
        // Validate avatar
        $this->validateAvatar($req);

        // Upload avatar
        return $this->uploadImage($req, $user);
    }

    /**
     * Update user email.
     *
     * @param $req
     * @param $user
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    private function updateEmail($req, $user)
    {
        // Validate email
        $this->validateEmail($req, $user->id);

        // Check if user must verify email or not
        $verify = false;
        if ($req->email != $user->email){
            $verify = true;
            $user->email_verified_at = null;
            $user->email = $req->email;
            $user->save();
            event(new Registered($user));
        }

        // Update email
        $user->fill(['email' => $req->email])->save();

        // Return ok
        return response()->json(['status' => 'ok', 'verify' => $verify],200);
    }

    /**
     * Update user password.
     *
     * @param $req
     * @param $user
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    private function updatePassword($req, $user)
    {
        // Validate password
        $this->validatePassword($req);

        // Check if current password match the one saved
        if (Hash::check($req->current_password, $user->password)){
            $user->fill(['password' => $req->password])->save();

            // Return ok
            return response()->json(['status' => 'ok','user' => new UserResource($user->fresh())],200);
        }

        // Return ko
        return response()->json(['status' => 'ko','user' => new UserResource($user->fresh())],200);
    }

    /**
     * Upload avatar.
     *
     * @param $req
     * @param $user
     * @return \Illuminate\Http\JsonResponse
     */
    private function uploadImage($req, $user)
    {
        if(Storage::disk('public')->exists($user->avatar_path)){
            Storage::disk('public')->delete($user->avatar_path);
        }
        $avatar = $req->file('file');
        $path = $avatar->hashName('avatars');
        $image = Image::make($avatar);
        $image->fit(128, 128, function ($constraint) {
            $constraint->aspectRatio();
        });
        Storage::disk('public')->put($path, (string) $image->encode());
        $user->avatar = $path;
        $user->save();

        // Return ok
        return response()->json(['status' => 'ok','user' => new UserResource($user->fresh())],200);
    }

    /**
     * Validate information.
     *
     * @param $req
     * @param $id
     * @return array
     * @throws \Illuminate\Validation\ValidationException
     */
    private function validateInformation($req, $id)
    {
        return $this->validate($req,['name' => 'required', 'phone' => 'nullable|digits:8|' . Rule::unique('users')->ignore($id, 'id')]);
    }

    /**
     * Validate avatar.
     *
     * @param $req
     * @return array
     * @throws \Illuminate\Validation\ValidationException
     */
    private function validateAvatar($req)
    {
        return $this->validate($req, ['file' => 'required|mimes:jpg,jpeg,png']);
    }

    /**
     * Validate email.
     *
     * @param $req
     * @param $id
     * @return array
     * @throws \Illuminate\Validation\ValidationException
     */
    private function validateEmail($req,$id)
    {
        return $this->validate($req,['email' => 'nullable|' . Rule::unique('users')->ignore($id, 'id')]);
    }

    /**
     * Validate password.
     *
     * @param $req
     * @return array
     * @throws \Illuminate\Validation\ValidationException
     */
    private function validatePassword($req)
    {
        return $this->validate($req, ['password' => 'required|string|min:5|confirmed', 'current_password' => 'required|string|min:5']);
    }
}
