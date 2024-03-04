<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            本部リクエスト
        </h2>
    </x-slot>
    <div class="max-w-7xl mx-auto px-6">
    <!-- 保存しましたのメッセージに対するcss設定 -->
        @if(session('honbumessage'))
            <div class="text-red-600 font-bold">
                {{session('honbumessage')}}
            </div>
        @endif
    <!------------------------------------------->
    <form method="post" action="{{ route('post.honbustore') }}">
    @csrf
        <div class="w-full flex flex-col">
            <label for="body" class="font-semibold mt-4">部署の特徴・求める人材を記載（箇条書き・文章でもOK）</label>
            <textarea name="honbubody" class="w-auto py-2 border border-gray-300 rounded-md" id="honbubody" cols="30" rows="5">
            </textarea>
        </div>

        <x-primary-button class="mt-4">
            送信する
        </x-primary-button>
    </form>
</div>
</x-app-layout>