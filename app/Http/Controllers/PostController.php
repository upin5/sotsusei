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
        if($inputText!=$request->previousBody){
        
            $responseText = $this->generateResponse($inputText);
            //sessionで入力内容を保持する処理1----------------------------------//
            // Session::put('inputText',$inputText);
            //----------------------------------------------------------------//
            $messages = [
                ['title' => '入力したテキスト','content'=>$inputText],
                ['title' => 'AIによるテキスト','content'=>$responseText]
            ];

            $request->session()->flash('inputText',$inputText);


            // AI判定と同時にDB登録する--------------------------//
            $post = UserRequest::create([
                'body' => $inputText,
                'gpt_response' => $responseText,
                'user_id' => auth()->id(),
            ]);
            // -------------------------------------------------//

            return view('post.create',['messages' => $messages]);
        }
        //DB使うパターンのログインしている人の投稿の最新情報を常に表示するための処理　> 動作しない----------------------------------//
        // $latestInputText = UserRequest::where('user_id', auth()->id())->latest()->value('body');
        //----------------------------------------------------------------------------------------//

        //セッション使って直前の入力を表示する処理2----------------------------------------------//
        // $inputText = Session::get('inputText','');
        //---------------------------------------------------------------------------------------//

        return view('post.create')->with('message','保存しました');
    }

    public function generateResponse($inputText){
        $client = new Client();
//以下はchatgptへの具体的な指示　使いまわしできる形　promptの書き換えにより調整可能　$inputtextはページ内の処理変数なので使いまわし可能 2/26記---------------------------------------------//
        $prompt = "以下の入力内容はスキルや得意分野を記載したものです。この人物の特徴をわかりやすく記載してください。" . $inputText . "特徴は他人が見て人物像が見えるように、かつ日本語で記載してください。";
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
//     }
}
//---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------//