<?php

namespace App\Http\Controllers;
use App\Post;
use Auth;
use Validator;
use Illuminate\Http\Request;

class PostsController extends Controller
{
    //コンストラクタ （このクラスが呼ばれると最初にこの処理をする）
    public function __construct()
    {
        // ログインしていなかったらログインページに遷移する（この処理を消すとログインしなくてもページを表示する）
        $this->middleware('auth');
    }
    public function index()
    {
        $posts = Post::limit(10)
            ->orderBy('created_at', 'desc')
            ->get();
            
         // テンプレート「post/index.blade.php」を表示します。
        return view('post/index', ['posts' => $posts]);
    }
    public function new()
    {
         // テンプレート「post/new.blade.php」を表示します。
        return view('post/new');
    }
    public function store(Request $request)
    {
        //バリデーション（入力値チェック）
        $validator = Validator::make($request->all() , ['caption' => 'required|max:255', 'photo' => 'required']);

        //バリデーションの結果がエラーの場合
        if ($validator->fails())
        {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        }

        // Postモデル作成
        $post = new Post;
        $post->caption = $request->caption;
        $post->user_id = Auth::user()->id;

        $post->save();
        
        $request->photo->storeAs('public/post_images', $post->id . '.jpg');
        
        // 「/」 ルートにリダイレクト
        return redirect('/');
    }
}
