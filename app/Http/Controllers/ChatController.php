<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use OpenAI\Laravel\Facades\OpenAI;
use GuzzleHttp\Client;

class ChatController extends Controller
{
    //chatgptのAPIキー使用テストのためのコントロール-----------
    public function chat(Request $request){
        $inputText=$request->food;
        if($inputText!=null){
            $responseText = $this->generateResponse($inputText);

            $messages = [
                ['title' => '食材', 'content' => $inputText],
                ['title' => 'レシピ', 'content' => $responseText]
            ];
            return view('chat.create',['messages' => $messages]);
        }
        return view('chat.create');
    }

    // public function generateResponse($inputText){
    //     $client = new Client();

    //     $result = OpenAI::completions()->create([
    //         'model' => 'gpt-3.5-turbo-0125',
    //         'prompt' => '冷蔵庫にある食材を教えます。'.$inputText.'美味しいレシピを日本語で教えてください',
    //         'temperature' => 0.8,
    //         'max-token' => 150,
    //     ]);
    //     return $result['choices'][0]['text'];
    // }
    public function generateResponse($inputText) {
        $client = new Client();
    //     $response = $client->post('https://api.openai.com/v1/chat/completions', [
    //         'headers' => [
    //             'Authorization' => 'Bearer ' . env('OPENAI_API_KEY'),
    //             'Content-Type' => 'application/json',
    //         ],
    //         'json' => [
    //             'model' => 'gpt-3.5-turbo',
    //             'messages' => [
    //                 ['role' => 'user', 'content' => $inputText],
    //             ],
    //         ],
    //     ]);
    //     $body = $response->getBody();
    //     $responseArray = json_decode($body, true);
    //     return $responseArray['choices'][0]['message']['content'];
    // }
    // 日本語でのプロンプトを設定
    $prompt = "以下の食材を使った美味しい料理レシピを教えてください: " . $inputText . "。レシピは簡単なもので、日本語で答えてください。";
    $response = $client->post('https://api.openai.com/v1/chat/completions', [
        'headers' => [
            'Authorization' => 'Bearer ' . env('OPENAI_API_KEY'),
            'Content-Type' => 'application/json',
        ],
        'json' => [
            'model' => 'gpt-3.5-turbo', // 使用するモデル
            'messages' => [
                ['role' => 'system', 'content' => "このチャットは日本語での料理レシピ提案を行います。"],
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
    // レスポンスから提案されたレシピを取得
    return $responseArray['choices'][0]['message']['content'];
}

}
