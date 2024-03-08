<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

//従業員がデータベースに登録するためのモデルファイルRequest.phpを参照-------
use App\Models\UserRequest;
//----------------------------------------------------------------------

//本部がデータベースに登録するためのモデルファイルHonbuRequest.phpを参照---
use App\Models\HonbuRequest;
//---------------------------------------------------------------------

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
    public function honbustore(Request $request){
        $post = HonbuRequest::create([
            'honbubody' => $request->honbubody
        ]);
        
        return back()->with('honbumessage', '保存しました');
    }
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
}
//-----------------------------------------------------------------------------------


//アルバイトで鍛えてたときに挫折したことがあるか。
//yse-noの質問は避ける
//人が好きなのか？こもるのが好きなのか？
//普段好きなことは？
//好きなことの延長に可能性を見る
//