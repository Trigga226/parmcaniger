<?php

namespace App\Http\Controllers;


use App\Http\Resources\Osaas\ProfilesCollection;
use App\Models\Permission;
use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
//80708679
class ProfilesController extends Controller
{
    /**
     * Manage profiles.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $title = 'Gestion des profils';
        $permissions = Permission::all();
        if(request()->wantsJson()){return Profile::index();}
        return view('user.profile',['title' => $title, 'user_permissions' => $permissions]);
    }

    /**
     * Store new profile.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|void
     */
    public function store(Request $request)
    {
        // Validate data
        $this->validateProfile($request);
        // Store profile
        DB::beginTransaction();
        try{
            $profile = new Profile();
            $profile->name = request('name');
            $profile->slug = Str::slug(request('name'));
            $profile->save();
            $profile->permissions()->sync(json_decode(request('permissions')));
            DB::commit();
            return response()->json(['OK'],200);
        }catch (Exception $e){
            DB::rollback();
            return abort(500);
        }
    }

    /**
     * Update  profile.
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse|void
     */
    public function update(Request $request, $id)
    {
        // Validate data
        $this->validateProfile($request,$id);
        // Update profile
        DB::beginTransaction();
        try{
            $profile = Profile::findOrFail($id);
            $profile->name = request('name');
            $profile->slug = Str::slug(request('name'));
            $profile->save();
            $profile->permissions()->sync(json_decode(request('permissions')));
            DB::commit();
            return response()->json(['OK'],200);
        }catch (Exception $e){
            DB::rollback();
            return abort(500);
        }
    }

    /**
     * Save profiler information.
     *
     * @param $profile
     */
    private function add($profile)
    {
        $profile->name = request('name');
        $profile->slug = Str::slug(request('name'));
    }

    /**
     * Validate profile data.
     *
     * @param Request $request
     * @param bool $id
     * @return mixed
     */
    private function validateProfile(Request $request,$id = null)
    {
        /*$rules =  ['permissions' => 'required'];
        if ($id){
            $rules['name'] = 'required|unique:profiles,name,'.$id;
        }else{
            $rules['name'] = 'required|name|unique:profiles';
        }*/
        //return $request->validate($rules);
    }

    public function destroy($id)
    {
        // Delete profile
        try{
            $profile = Profile::findOrFail($id);
            $profile->delete();
            return response()->json(['OK'],200);
        }catch (Exception $e){
            return abort(500);
        }
    }
}
