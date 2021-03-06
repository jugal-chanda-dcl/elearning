<?php

namespace App\Http\Controllers;

use App\TeacherProfile;
use Illuminate\Http\Request;
use Auth;
use Session;
use Carbon\Carbon;
use App\Profile;
class TeacherProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('teacherprofile.profile',['user'=>Auth::user()]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('teacherprofile.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
      $user = Auth::user();
      if ($user->teacherProfile)
      {
        Session::flash('status',"Already profile created. You can update your profile");
        return redirect()->back();
      }
      $now = Carbon::now();
      $validatedData = $request->validate([
        'year_of_experience' => 'required',
        'specilization' => 'required',
        'avatar' => 'required|image',
        'phone' => ['required', 'string', 'max:14', 'unique:profiles'],
        'profession' => ['required', 'string'],
        'address' => ['required', 'string'],
        'birthdate' => 'required|date|date_format:Y-m-d|before: $now',
      ]);
      $specilization = $request->input('specilization');
      $dom = new \DomDocument();
      $dom->loadHtml($specilization, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
      $images = $dom->getElementsByTagName('img');

      foreach($images as $k => $img){
        $data = $img->getAttribute('src');

          list($type, $data) = explode(';', $data);
          list(, $data)      = explode(',', $data);
          $data = base64_decode($data);

          $image_name= "/upload/" . time().$k.'.png';
          $path = public_path() . $image_name;
          file_put_contents($path, $data);
          $img->removeAttribute('src');
          $img->setAttribute('src', $image_name);

      }
      $specilization = $dom->saveHTML();

      $avatar = $request->avatar;
      $avatar_new_name = time().$avatar->getClientOriginalName();
      $avatar->move('upload/avatars',$avatar_new_name);

      $profile = Profile::create([
        'user_id' => $user->id,
        'phone' => $validatedData['phone'],
        'profession' => $validatedData['profession'],
        'address' => $validatedData['address'],
        'birthdate' => $validatedData['birthdate'],
        'age' => Carbon::parse($validatedData['birthdate'])->diffInDays($now)

      ]);
      $teacherProfile = TeacherProfile::create([
        'user_id' => $user->id,
        'year_of_experience' => $validatedData['year_of_experience'],
        'specilization' => $specilization,
        'avatar' => 'upload/avatars/'.$avatar_new_name,
      ]);
      return redirect()->route('teacherProfile.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\TeacherProfile  $teacherProfile
     * @return \Illuminate\Http\Response
     */
    public function show(TeacherProfile $teacherProfile)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\TeacherProfile  $teacherProfile
     * @return \Illuminate\Http\Response
     */
    public function edit(TeacherProfile $teacherProfile)
    {
        return view('teacherprofile.edit',['user'=>$teacherProfile->user]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\TeacherProfile  $teacherProfile
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, TeacherProfile $teacherProfile)
    {
      $user = Auth::user();
      $validatedData = $request->validate([
        'year_of_experience' => 'required',
        'specilization' => 'required',
        'phone' => ['required', 'string', 'max:14'],
        'profession' => ['required', 'string'],
        'address' => ['required', 'string'],
        'birthdate' => 'required|date|date_format:Y-m-d|before: $now',
        // 'avatar' => 'required|image'
      ]);
      // dd($validatedData['content']);
      $specilization = $request->input('specilization');
      // dd($specilization);
      $dom = new \DomDocument();
      // libxml_use_internal_errors(false);
      $dom->loadHtml($specilization, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
      $images = $dom->getElementsByTagName('img');

      foreach($images as $k => $img){
        $data = $img->getAttribute('src');
        if(strpos($data,'base64')!=false){
          list($type, $data) = explode(';', $data);
          list(, $data)      = explode(',', $data);
          $data = base64_decode($data);

          $image_name= "/upload/" . time().$k.'.png';
          $path = public_path() . $image_name;
          file_put_contents($path, $data);
          $img->removeAttribute('src');
          $img->setAttribute('src', $image_name);
        }
      }
      $specilization = $dom->saveHTML();

      $teacherProfile->year_of_experience = $validatedData['year_of_experience'];
      $teacherProfile->specilization = $specilization;
      if($request->avatar){
        $avatar = $request->avatar;
        $avatar_new_name = time().$avatar->getClientOriginalName();
        $avatar->move('upload/avatars',$avatar_new_name);
        $teacherProfile->avatar = 'upload/avatars/'.$avatar_new_name;
      }

      $teacherProfile->save();
      $profile = $user->profile;
      $profile->phone = $validatedData['phone'];
      $profile->profession = $validatedData['profession'];
      $profile->address = $validatedData['address'];
      $profile->birthdate = $validatedData['birthdate'];
      $profile->save();
      return redirect()->route('teacherProfile.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\TeacherProfile  $teacherProfile
     * @return \Illuminate\Http\Response
     */
    public function destroy(TeacherProfile $teacherProfile)
    {
        //
    }
}
