@extends('layouts.app')
<?php

//function getStatusColor($status) {
//
//    switch ($status) {
//        case 'Under_Finalization':
//            echo 'background-color: #ffffcb';
//            break;
//        case 'Approved':
//            echo 'background-color: #90ee90';
//            break;
//        case 'Under_Revision':
//            echo 'background-color: #add8e6';
//            break;
//        case 'Rejected':
//            echo 'background-color: #ffcccb';
//            break;
//        default:
//            echo 'background-color: #ffffff';
//    }
//}
?>
@extends('layouts.Toast')
@extends('components.reason-popup')
{{--get status color--}}
<?php
function GetStatusColor($status) {
    switch ($status) {
        case \App\Models\Document::UNDER_FINALIZATION:
            echo 'background-color: #ffffcb';
            break;
        case \App\Models\Document::APPROVED:
            echo 'background-color: #90ee90';
            break;
        case \App\Models\Document::UNDER_REVISION:
            echo 'background-color: #add8e6';
            break;
        case \App\Models\Document::REJECTED:
            echo 'background-color: #ffcccb';
            break;
        default:
            echo 'background-color: #ffffff';
    }
}
?>
@section('content')

{{--    upload form --}}
{{--check if role is uploader--}}
@if(auth()->user()->hasRole('uploader'))
    <div class="container mt-3">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Upload Document</div>

                    <div class="card-body">
                        <form action="{{route('create')}}" method="post" enctype="multipart/form-data">
                            @csrf

                            <div class="form-group  m-1 flex flex-wrap">
                                <div class="w-auto">
                                    <input id="file" type="file" class="form-control" name="file" required>
                                </div>
                                <div class="">
                                    <button type="submit" class="btn btn-primary">
                                        Upload
                                    </button>
                                </div>
                            </div>



                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif
{{--    table of documents--}}
    <br>
    <div class="container mt-3">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Home</div>

                    <div class="card-body">
                        <table class="table">
                            <thead>
                            <tr>
                                <th scope="col">Title</th>
                                <th scope="col">Version</th>
                                <th scope="col">File</th>

                                <th scope="col">status</th>
                                <th scope="col">Action</th>

                            </tr>
                            </thead>
                            <tbody>
                            @foreach($documents as $key => $document)
                                <tr @if($key % 2 == 1) style="background-color: rgb(224 231 255);" @endif>
                                    <td>{{$document->filename}}</td>
                                    <td>{{$document->version}}</td>
{{--                                    wrap in post form--}}
                                    <td>
                                        <form action="{{route('download', $document->id)}}" method="post">
                                            @csrf
                                            <button type="submit" class="btn btn-primary">Download</button>
                                        </form>
                                    </td>
{{--                                 td   background color depending on status--}}
                                    <td>
                                    <div id="status" class="justify-center rounded flex p-1 border-2 border-primary"  style=" <?php GetStatusColor($document->status) ?>">
                                        {{$document->status}}
                                    </div>
{{--                                   notifaction icon if there is a reason when hovered dislpay the reason in a text bubble--}}
                                        @if($document->reason)
                                            <div class="relative m-1">
                                                <div class="absolute top-0 right-0 bg-red-400 text-white rounded-full w-5 h-5 flex items-center justify-center">
                                                    <span class="text-xs">!</span>
                                                </div>
                                                <div class="hidden bg-red-400 text-white p-2 rounded-lg absolute top-0 right-0 -mt-8 -mr-8 yasser">
                                                    <span>{{$document->reason}}</span>
                                                </div>
                                            </div>
                                        @endif
                                    <td>

{{--                                        if uploader--}}
                                        @if(auth()->user()->hasRole('uploader') && $document->status == 'Under_Revision')
                                            <div class="flex flex-wrap ">
                                                <form action="{{route('edit', $document->id)}}" method="post">
                                                    @csrf
                                                    <button type="submit" class="btn btn-primary mr-1 ml-1">Edit</button>
                                                </form>
                                                <form action="{{route('delete', $document->id)}}" method="post">
                                                    @csrf
                                                    <button type="submit" class="btn btn-danger mr-1 ml-1">Delete</button>
                                                </form>
                                            </div>
                                        @endif
{{--                                        if reviewer--}}
                                        @if((auth()->user()->hasRole('reviewer') && $document->status == 'Under_Revision') || (auth()->user()->hasRole('finalizer') && $document->status == 'Under_Finalization'))
                                            <div class="flex flex-wrap ">
                                                <form action="{{route('approve')}}" method="post">
                                                    @csrf
                                                    <input type="hidden" name="id" value="{{$document->id}}">
                                                    <button type="submit" class="btn btn-primary mr-1 ml-1">Approve</button>
                                                </form>
{{--                                                button to open confirm with js reason popup--}}
                                                <button type="button" class="btn btn-danger mr-1 ml-1" onclick="openPopup({{'"'.$document->filename.'"'}})">Reject</button>
                                            </div>

                                        @endif


                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
<script>
    function openPopup(filename) {
        document.getElementById('popup').classList.remove('hidden');
        document.getElementById('popup-filename').value = filename;
    }
//     close popup on doc ready
    document.addEventListener('DOMContentLoaded', function() {
    //    close event listener
    //     close popup on click outside popup-body
        document.getElementById('popup-out').addEventListener('click', function (e) {
            if(e.target.id === 'popup-out') {
                document.getElementById('popup').classList.add('hidden');
            }
        });




    });

//     notifaction icon if there is a reason when hovered dislpay the reason in a text bubble
    document.addEventListener('DOMContentLoaded', function() {
        let notificationIcons = document.querySelectorAll('.relative');
        notificationIcons.forEach(function (icon) {
            if (icon.querySelector('.yasser') === null) {
                return;
            }
            icon.addEventListener('mouseover', function (e) {
                icon.querySelector('.hidden').classList.remove('hidden');
            });
            icon.addEventListener('mouseout', function (e) {
                icon.querySelector('.yasser').classList.add('hidden');
            });
        });
    });


</script>



@endsection
