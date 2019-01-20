<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

// 以下を追記することでProfile Modelが扱えるようになる
use App\Profile;

use App\Profilehistory;

use Carbon\Carbon;

class ProfileController extends Controller
{
    //
    public function edit(Request $request)
    {
      return view('admin.profile.edit');
    }

    public function update(Request $request)
    {
      // 以下を追記
      // Varidationを行う
      $this->validate($request, Profile::$rules);

      $profile = new Profile;
      $form = $request->all();
      \Debugbar::info($profile);


      // データベースに保存する
      $profile->fill($form);
      $profile->save();

      return redirect('admin/profile/edit');
    }
    // 以下を追記
  public function index(Request $request)
  {
      $cond_title = $request->cond_title;
      if ($cond_title != '') {
          // 検索されたら検索結果を取得する
          $posts = Profile::where('title', $cond_title)->get();
      } else {
          // それ以外はすべてのニュースを取得する
          $posts = Profile::all();
      }
      return view('admin.profile.index', ['posts' => $posts, 'cond_title' => $cond_title]);
  }

  public function doedit(Request $request)
    {
        // News Modelからデータを取得する
        $profile = Profile::find($request->id);

        return view('admin.profile.doedit', ['profile_form' => $profile]);
    }


    public function doupdate(Request $request)
    {
        // Validationをかける
        $this->validate($request, Profile::$rules);
        // Profile Modelからデータを取得する
        $profile = Profile::find($request->id);
        // 送信されてきたフォームデータを格納する
        $profile_form = $request->all();
        unset($profile_form['_token']);

        // 該当するデータを上書きして保存する
        $profile->fill($profile_form)->save();

        $profilehistory = new Profilehistory;
        $profilehistory->profile_id = $profile->id;
        $profilehistory->edited_at = Carbon::now();
        $profilehistory->save();

        return redirect('admin/profile/');

        return redirect('/admin/profile/');
    }
    public function delete(Request $request)
      {
          // 該当するProfile Modelを取得
          $profile = Profile::find($request->id);
          // 削除する
          $profile->delete();
          return redirect('/admin/profile/');
      }


}
