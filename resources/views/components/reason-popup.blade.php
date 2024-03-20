{{--popup--}}

<div id="popup" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50  z-30">
    <div id="popup-out" class="flex justify-center items-center h-full">
        <div class="bg-white p-8 rounded-lg w-1/3" id="popup-body z-40">
            <form action="{{route('reject')}}" method="post">
                @csrf
                <input type="hidden" name="popup-filename" id="popup-filename" value="">
                <div class="mb-4">
                    <label for="reason" class="sr-only">Reason</label>
{{--                    limit length of text area--}}
                    <textarea name="reason" id="reason" cols="30" rows="4" class="bg-gray-100 border-2 w-full p-4 rounded-lg @error('reason') border-red-500 @enderror" placeholder="Reason for rejection" style="max-height: 20rem;" maxlength="60000"></textarea>
                    @error('reason')
                    <div class="text-red-500 mt-2 text-sm">
                        {{$message}}
                    </div>
                    @enderror
                </div>
                <div class="flex justify-center">
                    <button type="submit" class="btn btn-danger">Reject</button>
                </div>
            </form>
        </div>
    </div>
</div>
