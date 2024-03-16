<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            検索結果
        </h2>
    </x-slot>

   
       

    <div class="bg-slate-300">
        <h1>検索結果</h1>
        @if($results->isEmpty())
            <p>検索結果はありません</p>
        
        @else
            <ul>
                @foreach($results as $result)
                <li>
                    <strong>{{ $result }}</strong>
                </li>
                @endforeach
            </ul>
        @endif
    </div>
    


</x-app-layout>