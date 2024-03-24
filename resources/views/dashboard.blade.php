<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('HOME') }}
        </h2>
    </x-slot>

    {{-- <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8"> --}}
            {{-- <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg"> --}}
                <div class="p-6 text-gray-900">
                    {{ __("ログインしました") }}
                </div>
                {{-- <div class="p-6 text-gray-900">
                    <x-primary-button>
                        ボタン
                    </x-primary-button>
            </div> --}}
        {{-- </div> --}}
    {{-- </div> --}}
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <div class="flex justify-center mt-10">
    <div class="w-1/3 h-1/2 bg-white shadow-lg sm:rounded-xl mr-2 cursor-pointer" id="jumptocreate" onclick="redirectToJumptocreate()">
        <p class="font-bold text-2xl mt-2 ml-2">自分登録</p>
        <p class="text-sm ml-2"><br>自分の得意なことなどを入力します。</p>
        <p class="text-sm ml-2">この入力により自分の人物像を作成します。</p>
        <p class="text-sm ml-2">AIが入力のお手伝いをします。</p>
        <p class="text-sm ml-2">AIがあなたの魅力を最大限に引き出します。</p>
        <br>
    </div>
    <div class="w-1/3 h-1/2 bg-white shadow-lg sm:rounded-xl mr-2 cursor-pointer" id="jumptohonbucreate" onclick="redirectToJumptohonbucreate()">
        <p class="font-bold text-2xl mt-2 ml-2">スカウト</p>
        <p class="text-sm ml-2"><br>部門（部署）が主に使う機能。</p>
        <p class="text-sm ml-2">必要な能力や人物像を作成します。</p>
        <p class="text-sm ml-2">作成はAIがサポートします。</p>
        <p class="text-sm ml-2">作成した人物像をもとに検索する機能です。</p>
        <br>
        
    </div>
    
    <script>
        function redirectToJumptocreate(){
            window.location.href = "{{ route('post.create') }}";
        }

        function redirectToJumptohonbucreate(){
            window.location.href = "{{ route('post.honbucreate') }}";
        }

    </script>



</x-app-layout>
