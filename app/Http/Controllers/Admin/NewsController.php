<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Aws\S3\S3Client;

use App\News;

use App\History;

use Carbon\Carbon;

class NewsController extends Controller
{
  public function add()
  {
      return view('admin.news.create');
  }

  public function create(Request $request)
  {
      // Varidationを行う
      $this->validate($request, News::$rules);

      $news = new News;
      $form = $request->all();



      // formに画像があれば、保存する
      if (isset($form['image'])) {
        $path =  $this->upload($request);
        $news->image_path = basename($path);
      } else {
          $news->image_path = null;
      }

      unset($form['_token']);
      unset($form['image']);
      // データベースに保存する
      $news->fill($form);
      $news->save();

      return redirect('admin/news/create');
  }

  public function index(Request $request)
  {
      $cond_title = $request->cond_title;
      if ($cond_title != '') {
          $posts = News::where('title', $cond_title)->get();
      } else {
          $posts = News::all();
      }
      return view('admin.news.index', ['posts' => $posts, 'cond_title' => $cond_title]);
  }


  public function edit(Request $request)
  {
      // News Modelからデータを取得する
      $news = News::find($request->id);

      return view('admin.news.edit', ['news_form' => $news]);
  }


  public function upload(Request $request)
  {
    //拡張子で画像でないファイルをはじく
    $ext = substr($_FILES['image']['name'], strrpos($_FILES['image']['name'], '.') + 1);
    if(strtolower($ext) !== 'png' && strtolower($ext) !== 'jpg' && strtolower($ext) !== 'gif'){
    echo '画像以外のファイルが指定されています。画像ファイル(png/jpg/jpeg/gif)を指定して下さい';
    echo $_FILES['image']['name'];
    echo $ext;
        exit();
    }
    //読み込みの際のキーとなるS3上のファイルパスを作る(作り方は色々あると思います)
    $tmpname = str_replace('/tmp/','',$_FILES['image']['tmp_name']);
    $new_filename = 'profiles/'.time().'-'.$tmpname.'.'.$ext;

    //S3clientのインスタンス生成(各項目の説明は後述)
    $s3client = S3Client::factory([
        'credentials' => [
          'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
        ],
        'region' => 'us-east-2',
        'version' => 'latest',
    ]);
    //バケット名を指定
    $bucket = getenv('S3_BUCKET_NAME')?: die('No "S3_BUCKET_NAME" config var in found in env!');
    //アップロードするファイルを用意
    $image = fopen($_FILES['image']['tmp_name'],'rb');

    //画像のアップロード(各項目の説明は後述)
    $result = $s3client->putObject([
        'ACL' => 'public-read',
        'Bucket' => $bucket,
        'Key' => $new_filename,
        'Body' => $image,
        'ContentType' => mime_content_type($_FILES['image']['tmp_name']),
    ]);

    //読み取り用のパスを返す
    $path = $result['ObjectURL'];
    return $path;
    //パスをDBに保存(ここの詳細処理は今回は記述しません)
    //$this->userRepository->updateUserProfsById($id, 'img_path', $path);
   }

  // 以下を追記　　
  public function delete(Request $request)
  {
     // 該当するNews Modelを取得
      $news = News::find($request->id);
      // 削除する
      $news->delete();
      return redirect('admin/news/');
    }
  }
