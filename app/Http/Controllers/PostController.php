<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;

//従業員がデータベースに登録するためのモデルファイルRequest.phpを参照-------
use App\Models\UserRequest;
//----------------------------------------------------------------------

//従業員がＤＢに2個目の質問を登録するためのモデルファイル参照---------------------------
use App\Models\UserRequest02;


//本部がデータベースに登録するためのモデルファイルHonbuRequest.phpを参照---
use App\Models\HonbuRequest;
//---------------------------------------------------------------------

//soukatsuページで評価と同時にＤＢ登録するためのモデルファイル
use App\Models\soukatsu;

//userテーブルからnameを取得するためのモデルファイル
use App\Models\User;

//chatgptを使うためのuse宣言---------------------------------
use OpenAI\laravel\Facades\OpenAI;
use GuzzleHttp\Client;
//----------------------------------------------------------



class PostController extends Controller
{
//create.blade.phpを表示するためのコントロール----------------------------    
    public function create(){
        return view('post.create');
    }
//----------------------------------------------------------------------

//create.blade.phpのformで送信されたデータを受け取る処理------------------//この処理をchatgptレスポンスと一緒に保存する処理に変える 2/26記//
    // public function userstore(Request $request){
    //     $post = UserRequest::create([
    //         'body' => $request->body
    //     ]);

    //     return back()->with('message', '保存しました');
    // }
//----------------------------------------------------------------------

//honbucreate.blade.phpを表示するためのコントロール-----------------------
    public function honbucreate(){
        return view('post.honbucreate');
    }
//----------------------------------------------------------------------

//honbucreate.blade.phpのformで送信されたデータを受け取る処理-------------
    // public function honbustore(Request $request){
    //     $post = HonbuRequest::create([
    //         'honbubody' => $request->honbubody
    //     ]);
        
    //     return back()->with('honbumessage', '保存しました');
    // }
//----------------------------------------------------------------------//







//chatgptのAPIキーを使うためのコントロール---------------------//
    public function userchat(Request $request){
        $inputText=$request->body;
        if($inputText!=null){
        
            $responseText = $this->generateResponse($inputText);
            
            // AI判定と同時にDB登録する--------------------------//
            $post = UserRequest::create([
                'body' => $inputText,
                'gpt_response' => $responseText,
                'user_id' => auth()->id(),
            ]);
            // -------------------------------------------------//
           
            $messages = [
                ['title' => '入力したテキスト','content'=>$inputText],
                ['title' => 'AIによるテキスト','content'=>$responseText]
            ];

           
//sessionで入力内容を保持する処理1----------------------------------//
Session::put('inputText',$inputText);
//----------------------------------------------------------------//

           
//----textareaにセッションで渡すコード-----//
$request->session()->flash('inputText',$inputText);
//---------------------------------------//
            // $latestUserRequest = UserRequest::where('user_id',auth()->id())->latest()->first();
            // return view('post.create', [
            //     'messages' => $messages,
            //     'latestUserRequest' => $latestUserRequest
            // ]);
            return view('post.create_kousin',['messages' => $messages]);
        }
        //DB使うパターンのログインしている人の投稿の最新情報を常に表示するための処理　> 動作しない----------------------------------//
        // $latestInputText = UserRequest::where('user_id', auth()->id())->latest()->value('body');
        //----------------------------------------------------------------------------------------//

        //セッション使って直前の入力を表示する処理2----------------------------------------------//
        // $inputText = Session::get('inputText','');
        //---------------------------------------------------------------------------------------//

        return view('post.create_kousin')->with('message','保存しました');
    }

    public function generateResponse($inputText){
        $client = new Client();
//以下はchatgptへの具体的な指示　使いまわしできる形　promptの書き換えにより調整可能　$inputtextはページ内の処理変数なので使いまわし可能 2/26記---------------------------------------------//
        $prompt = "以下に記載されているのは、事前に「今一番得意と思うことは何ですか？」という質問に対する答えです。答えを書いた人物を回答者とします。
                   AIにチェックしてほしいことは、回答者の記載中に活用の場面が記載されているか？ということですがない場合は回答者に対して質問してください。"
                 . $inputText . "日本語で記載してください。250トークン以内で。";
        $response = $client->post('https://api.openai.com/v1/chat/completions', [
            'headers' => [
                'Authorization' => 'Bearer ' . env('OPENAI_API_KEY'),
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'model' => 'gpt-3.5-turbo', // 使用するモデル
                'messages' => [
                    ['role' => 'system', 'content' => "このチャットは日本語での人物評価を行います。"],
                    ['role' => 'user', 'content' => $prompt],
                ],
                'temperature' => 0.7, // クリエイティビティの度合い
                'max_tokens' => 250, // 最大トークン数
                'top_p' => 1,
                'frequency_penalty' => 0,
                'presence_penalty' => 0,
                'stop' => ["\n"], // レスポンスの終了条件
            ],
        ]);
        $body = $response->getBody();
        $responseArray = json_decode($body, true);
        // レスポンスから取得した人物評価を記載
        return $responseArray['choices'][0]['message']['content'];
    }
//-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------//

//create.blade.phpの登録ボタンを押下したときにデータベースに登録する処理----------------------------------------------------------------------------------------------------------------//
//     public function userStoreAction(Request $request){
//             $inputText = $this->generateResponse($request->body);
//             $responseText = $this->generateResponse($request->gpt_response);
//             $post = UserRequest::create([
//                 'body' => $inputText,
//                 'gpt_response' => $responseText,
//             ]);
//             return back()->with('message','登録しました');
        
//         return view('post.create');
    // }

//---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------//

//---学生のときに力を入れていたこと-------------------------------------------//
//---賞をとったことがあることに対して、どこに注力したのか？プロセスを聞く-------//
//---活用したこと、もの-------//
//---チャレンジジョブ→　仕事にかけている思い。何を成し遂げたいと思っているのか？成し遂げたら何がどう変わるのか？エモーショナルな部分を聞く//
//---強み→期待されている仕事は何か？　スキルは何を持っているかと何に使えると思っているか？//
//---課題→今後身に着けたい能力は何か？----------//
//---マインド、身に着けたいスキル、具体的には？、使う場面？成果があったか？成果の大きさ？何と比較して得意だと言っているのか？----//
//---　------//

//具体性の定義を入れないとダメ。これ後。
//uiを分ける
//プロフェッショナルにやりとりを聞く。分けるべきところは分ける。
//定義にそったやりとりを重視。効率、効果。
//生成ＡＩに何をさせたいか？どういうことを最後に出してもらうか？
//バリエーション、パターン。ロールプレイングしてレコーディングしてワードを引き出す。新入社員だったら？社外は？


//chatgptのAPIキーを使うためのコントロールで更新処理部分---------------------//
public function update(Request $request){
    $inputText=$request->body;
    if($inputText!=null){
    
        $responseText = $this->generateResponsekousin($inputText);
        
        $findRequest = UserRequest::latest()->first();

        if($findRequest){
            $findRequest->body = $inputText;
            $findRequest->gpt_response = $responseText;
            $findRequest->save();
        }

        // AI判定と同時にDB登録する--------------------------//
        // $post = UserRequest::save([
        //     'body' => $inputText,
        //     'gpt_response' => $responseText,
        //     'user_id' => auth()->id(),
        // ]);
        // -------------------------------------------------//
       
        $messages = [
            ['title' => '入力したテキスト','content'=>$inputText],
            ['title' => 'AIによるテキスト','content'=>$responseText]
        ];

        
//sessionで入力内容を保持する処理1----------------------------------//
Session::put('inputText',$inputText);
//----------------------------------------------------------------//

       
//----textareaにセッションで渡すコード-----//
$request->session()->flash('inputText',$inputText);
//---------------------------------------//
        // $latestUserRequest = UserRequest::where('user_id',auth()->id())->latest()->first();
        // return view('post.create', [
        //     'messages' => $messages,
        //     'latestUserRequest' => $latestUserRequest
        // ]);
        return view('post.create_kousin',['messages' => $messages]);
    }
    //DB使うパターンのログインしている人の投稿の最新情報を常に表示するための処理　> 動作しない----------------------------------//
    // $latestInputText = UserRequest::where('user_id', auth()->id())->latest()->value('body');
    //----------------------------------------------------------------------------------------//

    //セッション使って直前の入力を表示する処理2----------------------------------------------//
    // $inputText = Session::get('inputText','');
    //---------------------------------------------------------------------------------------//

    return view('post.create_kousin')->with('message','保存しました');
}

public function generateResponsekousin($inputText){
    $client = new Client();
//以下はchatgptへの具体的な指示　使いまわしできる形　promptの書き換えにより調整可能　$inputtextはページ内の処理変数なので使いまわし可能 2/26記---------------------------------------------//
    $prompt = "以下に記載されているのは、事前に「今一番得意と思うことは何ですか？」という質問に対する答えです。答えを書いた人物を回答者とします。
              AIにチェックしてほしいことは、回答者の記載中に活用の場面が記載されているか？ということですがない場合は回答者に対して質問してください。"
            . $inputText . "日本語で記載してください。250トークン以内で。";
    $response = $client->post('https://api.openai.com/v1/chat/completions', [
        'headers' => [
            'Authorization' => 'Bearer ' . env('OPENAI_API_KEY'),
            'Content-Type' => 'application/json',
        ],
        'json' => [
            'model' => 'gpt-3.5-turbo', // 使用するモデル
            'messages' => [
                ['role' => 'system', 'content' => "このチャットは日本語での人物評価を行います。"],
                ['role' => 'user', 'content' => $prompt],
            ],
            'temperature' => 0.7, // クリエイティビティの度合い
            'max_tokens' => 250, // 最大トークン数
            'top_p' => 1,
            'frequency_penalty' => 0,
            'presence_penalty' => 0,
            'stop' => ["\n"], // レスポンスの終了条件
        ],
    ]);
    $body = $response->getBody();
    $responseArray = json_decode($body, true);
    // レスポンスから取得した人物評価を記載
    return $responseArray['choices'][0]['message']['content'];
}

//次の質問をクリックするとcreate02へ遷移するためのコントローラー----//
public function jumpcreate02(){
    return view('post.create02');
}


//create02ページのchatgptのAPIキーを使うためのコントロール---------------------//
public function userchat02(Request $request){
    $inputText02=$request->body02;
    if($inputText02!=null){
    
        $responseText02 = $this->generateResponse02($inputText02);
        
        // AI判定と同時にDB登録する--------------------------//
        $post = UserRequest02::create([
            'body02' => $inputText02,
            'gpt_response02' => $responseText02,
            'user_id' => auth()->id(),
        ]);
        // -------------------------------------------------//
       
        $messages = [
            ['title' => '入力したテキスト','content'=>$inputText02],
            ['title' => 'AIによるテキスト','content'=>$responseText02]
        ];

       
//sessionで入力内容を保持する処理1----------------------------------//
Session::put('inputText02',$inputText02);
//----------------------------------------------------------------//

       
//----textareaにセッションで渡すコード-----//
$request->session()->flash('inputText02',$inputText02);
//---------------------------------------//
        // $latestUserRequest = UserRequest::where('user_id',auth()->id())->latest()->first();
        // return view('post.create', [
        //     'messages' => $messages,
        //     'latestUserRequest' => $latestUserRequest
        // ]);
        return view('post.create02_kousin',['messages' => $messages]);
    }
    //DB使うパターンのログインしている人の投稿の最新情報を常に表示するための処理　> 動作しない----------------------------------//
    // $latestInputText = UserRequest::where('user_id', auth()->id())->latest()->value('body');
    //----------------------------------------------------------------------------------------//

    //セッション使って直前の入力を表示する処理2----------------------------------------------//
    // $inputText = Session::get('inputText','');
    //---------------------------------------------------------------------------------------//

    return view('post.create02_kousin')->with('message','保存しました');
}

//以下に記載されているのは、事前に「スキルを活用する場面で何か工夫したことはありますか？」という質問に対する答えです。答えを書いた人物を回答者とします。
//AIにチェックしてほしいことは、回答者が記載した回答の中に工夫をした理由が記載されているか？ということです。AIから見て理由が見当たらない場合、AIが回答者へ工夫をした理由を質問してください。

public function generateResponse02($inputText02){
    $client = new Client();
//以下はchatgptへの具体的な指示　使いまわしできる形　promptの書き換えにより調整可能　$inputtextはページ内の処理変数なので使いまわし可能 2/26記。promptは端的に---------------------------------------------//
    $prompt = "以下に記載されているのは、事前に「スキルを活用する場面で何か工夫したことはありますか？」という質問に対する答えです。答えを書いた人物を回答者とします。
    AIにチェックしてほしいことは、回答者が記載した回答の中に工夫をした理由が記載されているか？ということです。AIから見て理由が見当たらない場合、AIが回答者へ工夫をした理由を質問してください。"   
             . $inputText02 . "日本語で記載してください。250トークン以内で。";
    $response = $client->post('https://api.openai.com/v1/chat/completions', [
        'headers' => [
            'Authorization' => 'Bearer ' . env('OPENAI_API_KEY'),
            'Content-Type' => 'application/json',
        ],
        'json' => [
            'model' => 'gpt-3.5-turbo', // 使用するモデル
            'messages' => [
                ['role' => 'system', 'content' => "このチャットは日本語での人物評価を行います。"],
                ['role' => 'user', 'content' => $prompt],
            ],
            'temperature' => 0.7, // クリエイティビティの度合い
            'max_tokens' => 250, // 最大トークン数
            'top_p' => 1,
            'frequency_penalty' => 0,
            'presence_penalty' => 0,
            'stop' => ["\n"], // レスポンスの終了条件
        ],
    ]);
    $body02 = $response->getBody();
    $responseArray = json_decode($body02, true);
    // レスポンスから取得した人物評価を記載
    return $responseArray['choices'][0]['message']['content'];
}


//2つ目の質問 create02_kousin.blade.phpでデータベースへ更新処理をする部分---------------------------------------------------------------------
public function update02(Request $request){
    $inputText02=$request->body02;
    if($inputText02!=null){
    
        $responseText02 = $this->generateResponsekousin02($inputText02);
        
        $findRequest02 = UserRequest02::latest()->first();

        if($findRequest02){
            $findRequest02->body02 = $inputText02;
            $findRequest02->gpt_response02 = $responseText02;
            $findRequest02->save();
        }

        // AI判定と同時にDB登録する--------------------------//
        // $post = UserRequest::save([
        //     'body' => $inputText,
        //     'gpt_response' => $responseText,
        //     'user_id' => auth()->id(),
        // ]);
        // -------------------------------------------------//
       
        $messages = [
            ['title' => '入力したテキスト','content'=>$inputText02],
            ['title' => 'AIによるテキスト','content'=>$responseText02]
        ];

        
//sessionで入力内容を保持する処理1----------------------------------//
Session::put('inputText02',$inputText02);
//----------------------------------------------------------------//

       
//----textareaにセッションで渡すコード-----//
$request->session()->flash('inputText02',$inputText02);
//---------------------------------------//
        // $latestUserRequest = UserRequest::where('user_id',auth()->id())->latest()->first();
        // return view('post.create', [
        //     'messages' => $messages,
        //     'latestUserRequest' => $latestUserRequest
        // ]);
        return view('post.create02_kousin',['messages' => $messages]);
    }
    //DB使うパターンのログインしている人の投稿の最新情報を常に表示するための処理　> 動作しない----------------------------------//
    // $latestInputText = UserRequest::where('user_id', auth()->id())->latest()->value('body');
    //----------------------------------------------------------------------------------------//

    //セッション使って直前の入力を表示する処理2----------------------------------------------//
    // $inputText = Session::get('inputText','');
    //---------------------------------------------------------------------------------------//

    return view('post.create02_kousin')->with('message','保存しました');
}

public function generateResponsekousin02($inputText02){
    $client = new Client();
//以下はchatgptへの具体的な指示　使いまわしできる形　promptの書き換えにより調整可能　$inputtextはページ内の処理変数なので使いまわし可能 2/26記---------------------------------------------//
    $prompt = "以下に記載されているのは、事前に「今一番得意と思うことは何ですか？」という質問に対する答えです。答えを書いた人物を回答者とします。
              AIにチェックしてほしいことは、回答者が記載した回答の中に工夫をした理由が記載されているか？ということです。AIから見て理由が見当たらない場合、AIが回答者へ工夫をした理由を質問してください。"
            . $inputText02 . "日本語で記載してください。250トークン以内で。";
    $response = $client->post('https://api.openai.com/v1/chat/completions', [
        'headers' => [
            'Authorization' => 'Bearer ' . env('OPENAI_API_KEY'),
            'Content-Type' => 'application/json',
        ],
        'json' => [
            'model' => 'gpt-3.5-turbo', // 使用するモデル
            'messages' => [
                ['role' => 'system', 'content' => "このチャットは日本語での人物評価を行います。"],
                ['role' => 'user', 'content' => $prompt],
            ],
            'temperature' => 0.7, // クリエイティビティの度合い
            'max_tokens' => 250, // 最大トークン数
            'top_p' => 1,
            'frequency_penalty' => 0,
            'presence_penalty' => 0,
            'stop' => ["\n"], // レスポンスの終了条件
        ],
    ]);
    $body02 = $response->getBody();
    $responseArray = json_decode($body02, true);
    // レスポンスから取得した人物評価を記載
    return $responseArray['choices'][0]['message']['content'];
}

//-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------//

//create02_kousinの次の質問をクリックすると総括ページへ飛ぶ------------//
public function jumpcreatesoukatsu(){
    return view('post.create_soukatsu');
}
//------------------------------------------------------------------//

//データベースから従業員投稿の複数のデータを取得して、chatgptに投げて総括コメントをもらうための処理--------//
public function userchat03(){
    $userId = Auth::id();

    $lastPost1 = UserRequest::where('user_id', $userId)
                                ->orderBy('created_at','desc')
                                // ->first();
                                ->value('body');

    $lastPost2 = UserRequest02::where('user_id',$userId)
                                ->orderBy('created_at', 'desc')
                                // ->first();
                                ->value('body02');

    
                                $client = new Client();
                                //以下はchatgptへの具体的な指示　使いまわしできる形　promptの書き換えにより調整可能　$inputtextはページ内の処理変数なので使いまわし可能 2/26記---------------------------------------------//
                                    $prompt = "以下はログインユーザーのスキル内容と工夫したことが記載された内容です。これらの情報をもとに人物の評価をしてください。評価は人物の内面的な特性の推測もあるとより一層いいです。"
                                            . $lastPost1 . $lastPost2 . "日本語で記載してください。250トークン以内で。";
                                    $response = $client->post('https://api.openai.com/v1/chat/completions', [
                                        'headers' => [
                                            'Authorization' => 'Bearer ' . env('OPENAI_API_KEY'),
                                            'Content-Type' => 'application/json',
                                        ],
                                        'json' => [
                                            'model' => 'gpt-3.5-turbo', // 使用するモデル
                                            'messages' => [
                                                ['role' => 'system', 'content' => "このチャットは日本語での人物評価を行います。"],
                                                ['role' => 'user', 'content' => $prompt],
                                            ],
                                            'temperature' => 0.7, // クリエイティビティの度合い
                                            'max_tokens' => 500, // 最大トークン数
                                            'top_p' => 1,
                                            'frequency_penalty' => 0,
                                            'presence_penalty' => 0,
                                            'stop' => ["\n"], // レスポンスの終了条件
                                        ],
                                    ]);
                                    
                                    $responseArray = json_decode($response->getbody(), true);
                        
                                    // レスポンスから取得した人物評価を記載
                                    $analysisResult = $responseArray['choices'][0]['message']['content'];

                                    //DB登録-----------------------------------------------------------------
                                    $soukatsu = new Soukatsu();
                                    $soukatsu->user_id = $userId;
                                    $soukatsu->gpt_text = $analysisResult;
                                    $saved = $soukatsu->save();

                                    //登録しましたの文字表示
                                    if($saved){
                                        $tourokumessage = "登録しました";

                                    }else{
                                        $tourokumessage = "登録に失敗しました";
                                    }

                                    return view('post.create_soukatsu',['analysisResult' => $analysisResult, 'tourokumessage' => $tourokumessage]);
                                }
                                //--------------------------------------------------------------------------------------//

                                //以下はhonbucreateから送られたデータを取得し、chatgptに投げるための事前処理----------------//
                                public function process(Request $request)
                                {
                                    // JavaScriptから送信されたデータを取得
                                    $selected_words = $request->input('selected_words');
                                    $selected_words_2 = $request->input('selected_words_2');
                                    $selected_words_3 = $request->input('selected_words_3');
                                    // $selected_words_4 = $request->input('selected_words_4');
                                    $honbubody = $request->input('honbubody');
                            
                                    // ChatGPTにデータを渡す処理----------------------------------------------------------//
                                    $client = new Client();

                                    //↓書きかけ！！ここからまだ構築できていません！！-------//
                                    //以下はchatgptへの具体的な指示　使いまわしできる形　promptの書き換えにより調整可能　$inputtextはページ内の処理変数なので使いまわし可能 2/26記---------------------------------------------//
                                        $prompt = "以下の入力は、入力者が探している人物の特徴を示したものです。AIにしてもらいたいことは、入力された情報を元に人物像を表現することです。
                                        "
                                                . $selected_words . $selected_words_2 . $selected_words_3 . $honbubody . "日本語で表現してください。250トークン以内で。";
                                        $response = $client->post('https://api.openai.com/v1/chat/completions', [
                                            'headers' => [
                                                'Authorization' => 'Bearer ' . env('OPENAI_API_KEY'),
                                                'Content-Type' => 'application/json',
                                            ],
                                            'json' => [
                                                'model' => 'gpt-3.5-turbo', // 使用するモデル
                                                'messages' => [
                                                    ['role' => 'system', 'content' => "このチャットは日本語での人物評価を行います。"],
                                                    ['role' => 'user', 'content' => $prompt],
                                                ],
                                                'temperature' => 0.3, // クリエイティビティの度合い
                                                'max_tokens' => 500, // 最大トークン数
                                                'top_p' => 1,
                                                'frequency_penalty' => 0,
                                                'presence_penalty' => 0,
                                                'stop' => ["\n"], // レスポンスの終了条件
                                            ],
                                        ]);
                                        
                                        $responseArray = json_decode($response->getbody(), true);
                            
                                        // レスポンスから取得した人物評価を記載
                                        $analysisResult02 = $responseArray['choices'][0]['message']['content'];

                                        return view('post.honbucreate',['analysisResult02' => $analysisResult02]);
                            
                                    // 処理が完了した後、適切なレスポンスを返す
                                    // return response()->json(['message' => 'Data processed successfully']);
                                }

//honbucreateのai生成された人物像をもとにsoukatsuテーブルから似た人物を検索するための処理---------------//
public function index(Request $request)
{
    $honbubody02 = $request->input('honbubody02');
    $results = soukatsu::whereRaw("MATCH(gpt_text) AGAINST(? IN BOOLEAN MODE)", [$honbubody02])
    ->take(3)
    ->get();

    //最低でも
    // if ($results->isEmpty()) {
    //     $fallbackResult = soukatsu::inRandomOrder()->first();
    //     $results->push($fallbackResult);
    // }



    $formattedResults = $results->map(function($result){
        $user = User::find($result->user_id);
        return[
            'gpt_text' => $result->gpt_text,
            'user_name' => $user ? $user->name : 'Unknown User'
        ];
    });

    return view('post.search_results' , ['results' => $formattedResults]);
}








}













//-----------------------------------------------------------------------------------


//アルバイトで鍛えてたときに挫折したことがあるか。
//yse-noの質問は避ける
//人が好きなのか？こもるのが好きなのか？
//普段好きなことは？
//好きなことの延長に可能性を見る
//

