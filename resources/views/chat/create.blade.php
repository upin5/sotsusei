<!DOCTYPE html>
<html lang="ja">
<head>
    <title>レシピ作成アプリ</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div>
        {{-- 材料を入力するためのフォーム --}}
        <form action="{{route('chat.post')}}" method="POST">
            @csrf
            <input name="food" type="text">
            <div>
            <button type="submit" style="padding:0.4em;background-color:green;color:white;">
                送信する
            </button>
            <button id="clearChatButton" style="padding:0.4em;background-color:red;color:white;">
                履歴削除
            </button>
        </div>
        </form>
            {{--ChatGPTの回答を表示 --}}
            @isset($messages)
            <div id="chat-contents">
                @foreach($messages as $message)
                    <div>
                       {{ $message['title'] }}: {{ $message['content'] }}
                    </div>
                @endforeach
            </div>
            @endisset
        </div>
    </div>
    {{-- 履歴削除ボタンをクリックすると、ChatGPTの回答（chat-contents内）を削除するスクリプト --}}
    <script>
        document.getElementById('clearChatButton').addEventListener('click', function() {
            const chatContainer = document.getElementById('chat-contents');
            chatContainer.innerHTML = '';
        });
    </script>
</body>
</html>