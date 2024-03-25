@section('components.search-bar')
    {{--search Bar--}}
    <div class="w-100 flex justify-center m-4" >
    <form action="{{route('search')}}" method="get">
        @csrf
        <div class="input-group mb-3">
            <input type="text" class="form-control" name="query" placeholder="Search">
            <div class="input-group-append">
                <button class="btn btn-outline-secondary" type="submit">Search</button>
            </div>
        </div>
    </form>
    </div>
@endsection


