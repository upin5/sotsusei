<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            リクエスト
        </h2>
    </x-slot>
    {{-- ユーザーが自分の答えを見つけ出すまでの道筋をimgで表示する ------------}}

        

    {{----------------------------------------------------------------------}}
    
    <!-- 保存しましたのメッセージに対するcss設定 ------------------------------>
    <div class="max-w-7xl mx-auto px-6">
        @if(session('message'))
            <div class="text-red-600 font-bold">
                {{session('message')}}
            </div>
        @endif
    <!----------------------------------------------------------------------->

    {{-- ↓元の送信コード --}}
    {{-- <form method="post" action="{{ route('user.post.userstore') }}"> --}}
    
    {{-- @if($latestUserRequest)
    <p>{{ $latestUserRequest->body}}</p>
    @endif --}}



    {{-- ↓chatgptに投げるコード ---------------------------------------------}}
    <form method="post" action="{{route('userpost02.chat')}}">
    {{----------------------------------------------------------------------}}
    @csrf
        <div class="w-full flex flex-col">
            <label for="body02" class="font-semibold mt-4">質問2:スキルを活用する場面で工夫したことは何ですか？また工夫した理由が知りたいです。</label>
            <textarea name="body02" class="w-auto py-2 border border-gray-300 rounded-md" id="body02" cols="30" rows="5">
            @if(session('inputText02'))
            {{session('inputText02')}}
            @endif 

            </textarea>
            {{-- <input type="hidden" id="previousBody" name="previousBody" value="{{ old('previousBody') }}"> --}}
        </div>

        <x-primary-button class="mt-4">
            AI判定
        </x-primary-button>
        {{-- <x-primary-button class="mt-4" id="clearChat">
            削除する
        </x-primary-button> --}}
    </form>
    <form method="post" action="{{route('user.store02')}}">
    @csrf
        {{-- ↓登録するを押下するとデータベースに自身の入力値"body"とchatgptの$message[content]を保存する処理をcontrollerに追加する 2/26記 --}}
        {{--　データベースにカラム追加する必要あり 2/26記 --}}
        {{-- <x-primary-button type="submit" class="mt-4">
            更新する
        </x-primary-button>
        {{----------------------------------------------------------------------------------------------------------------------}}

        {{-- <p class="font-semibold">更新するをクリックすると最新の入力内容が登録されます</p> --}}

    </form>
      {{--ChatGPTの回答を表示 ------------------------------------------------------------}}
      @isset($messages)
      <div id="chat-contents">
          @foreach($messages as $message)
              <div>
                 {{ $message['title'] }}: {{ $message['content'] }}
              </div>
          @endforeach
      </div>
      @endisset
      {{---------------------------------------------------------------------------------}}
</div>

      {{-- <script>
        document.addEventListener("DOMContentLoaded", function(){
            var previousTextAreaValue = document.getElementById("body").value;
            document.getElementById("previousBody").value = previousTextAreaValue;
        });
      </script> --}}




      {{-- clearchatを押したときに内容を消す処理 ------------------------------------------}}
      <script>
        document.addEventListener("DOMContentLoaded", function() {
        // clearChatボタンのクリックイベントを監視------------------------------------------//
        document.getElementById("clearChat").addEventListener("click", function() {
        // chatContent要素の中身を空にする-------------------------------------------------//
        document.getElementById("chat-contents").innerHTML = "";
    });
});
      </script>
      {{---------------------------------------------------------------------------------}}
    
</x-app-layout>