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
        @foreach($results as $result)
        
            {{-- <li> --}}
                {{-- @foreach($results as $result)
                <li>
                    <strong>{{ $result }}</strong>
                </li>
                @endforeach --}}
                <textarea style="width: 100%; height:100%; background-color:lemonchiffon" rows="5">@foreach($result as $item) {{ $item }}
                    @endforeach
                </textarea>
            {{-- </li> --}}
        
        @endforeach
        @endif
    </div>
    


</x-app-layout>