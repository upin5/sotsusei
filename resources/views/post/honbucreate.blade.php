<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            本部リクエスト
        </h2>
    </x-slot>
    {{-- <div class="max-w-7xl mx-auto px-6"> --}}
    <!-- 保存しましたのメッセージに対するcss設定 -->
        {{-- @if(session('honbumessage'))
            <div class="text-red-600 font-bold">
                {{session('honbumessage')}}
            </div>
        @endif --}}
    <!------------------------------------------->
    {{-- <form method="post" action="{{ route('post.honbustore') }}">
    @csrf --}}
        {{-- <div class="w-full flex flex-col">
            <label for="body" class="font-semibold mt-4">部署の特徴・求める人材を記載（箇条書き・文章でもOK）</label>
            <textarea name="honbubody" class="w-auto py-2 border border-gray-300 rounded-md" id="honbubody" cols="30" rows="5">
            </textarea>
        </div> --}}
    <div>
        <p class="ml-3 mt-4 px-2 font-semibold">
            探す人物の基本情報を選択してください
        </p>
    </div>
    <form method="post" action="{{ route('process.chat') }}">
        @csrf
    <div class="flex">

        <div class="mt-4 ml-4">
            <label for="selected_words">基本スキル</label>
            <select name="selected_words" id="selected_words" class="p-2 w-48">
                <option>計算が得意</option>
                <option>文章が得意</option>
                <option>説明が得意</option>
                <option>絵を描くことが得意</option>
            </select>
        </div>
    
        <div class="mt-4 ml-4">
            <label for="selected_words_2">業務への活用例</label>
            <select name="selected_words_2" id="selected_words_2" class="p-2 w-48">
                <option>予算を作成</option>
                <option>資料を作成</option>
                <option>教育をする</option>
                <option>ポップを作る</option>
            </select>
        </div>

        <div class="mt-4 ml-4">
            <label for="selected_words_3">内面の特性</label>
            <select name="selected_words_3" id="selected_words_3" class="p-2 w-48">
                <option>几帳面</option>
                <option>積極的</option>
                <option>創造的</option>
                <option>明るい</option>
            </select>


        </div>
    </div>
    {{-- ルート見直し必要！！！！ --}}
    <div>
         
        <div class="w-full flex flex-col mt-10">
            <label for="honbubody" class="font-semibold ml-4 mt-4">上記以外で補足したい情報を記載（50文字程度で）</label>
            <textarea name="honbubody" class="w-auto py-2 border border-gray-300 rounded-md" id="honbubody" cols="30" rows="5">
            </textarea>
        </div>
    </div>
    
        <x-primary-button id="ai-button" class="mt-4 ml-4">
            AI
        </x-primary-button>

  
</div>
</form>
<!-- JavaScript -->

{{-- <script>
     document.querySelector('ai-button').addEventListener('click', function() {
        document.querySelector('form').submit();
        var selected_words = document.getElementById('selected_words').value;
        var selected_words_2 = document.getElementById('selected_words_2').value;
        var selected_words_3 = document.getElementById('selected_words_3').value;
        var honbubody = document.getElementById('honbubody').value;

            // ChatGPTに送信するデータの構築
            var prompt_text = "基本スキル: " + selected_words + "\n";
                prompt_text += "業務への活用例: " + selected_words_2 + "\n";
                prompt_text += "内面の特性: " + selected_words_3 + "\n";
                prompt_text += "補足したい情報: " + honbubody;
    });
</script> --}}



    {{-- aiのレスポンスを表示するスペース ルート見直す必要！！！！！--}}
<div class="bg-slate-300">
    <form method="post" action="{{ route('index.post') }}">
@csrf
   <div class="w-full flex flex-col bg-slate-300">
       <label for="honbubody02" class="font-semibold ml-4 mt-4 bg-slate-300">AI生成による検索する人物像</label>
       <textarea name="honbubody02" class="w-auto mb-8 py-2 border border-gray-300 rounded-md" id="honbubody02" cols="30" rows="5">
        {{ $analysisResult02 ?? '' }}
       </textarea>
   </div>
</div>
<div class="bg-slate-300">
<x-primary-button class="mt-2 ml-4 mb-4">
    検索
</x-primary-button>
</div>
    </form>
{{-- <script> --}}
    {{-- // AIボタンがクリックされたときの処理 --}}
    {{-- // document.addEventListener('DOMContentLoaded', function() {
    //     document.querySelector('.primary-button').addEventListener('click', function() {
    //         // 選択された値の取得
    //         var selected_words = document.getElementById('selected_words').value;
    //         var selected_words_2 = document.getElementById('selected_words_2').value;
    //         var selected_words_3 = document.getElementById('selected_words_3').value;
    //         var honbubody = document.getElementById('honbubody').value;

    //         // ChatGPTに送信するデータの構築
    //         var prompt_text = "基本スキル: " + selected_words + "\n";
    //         prompt_text += "業務への活用例: " + selected_words_2 + "\n";
    //         prompt_text += "内面の特性: " + selected_words_3 + "\n";
    //         prompt_text += "補足したい情報: " + honbubody;

            // ChatGPTにデータを送信する処理（ここでは省略）
//             var formData = new FormData();
// formData.append('prompt_text', prompt_text);

// // Ajaxリクエストを作成してサーバーにデータを送信
// var xhr = new XMLHttpRequest();
// xhr.open('POST', 'app/Http/Controllers/PostController.php'); // サーバーのコントローラーのURLを指定する
// xhr.setRequestHeader('X-CSRF-TOKEN', '{{ csrf_token() }}'); // CSRFトークンを設定する
// xhr.onreadystatechange = function() {
//     if (xhr.readyState === XMLHttpRequest.DONE) {
//         if (xhr.status === 200) {
//             console.log('データが正常に送信されました');
//             // 成功時の処理を記述する
//         } else {
//             console.error('データの送信中にエラーが発生しました');
//             // エラー時の処理を記述する
//         }
//     }
// };
// xhr.send(formData);
            
    //     });
    // }); --}}
{{-- </script> --}}

</x-app-layout>