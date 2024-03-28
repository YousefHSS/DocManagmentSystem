<?php

namespace App\Http\Controllers;

use App\Mail\DocumentAction;
use App\Models\Document;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class EmailController extends Controller
{
    static public function BuildSendMail($doc , $action){

        switch ($doc->status){
            case Document::UNDER_REVISION:
                $R_emails = User::query()->whereHas('roles', function ($query) {
                    $query->where('role_slug', 'reviewer');
                })->first();
                if ($R_emails != null)
                    Mail::to($R_emails->email)->send(new DocumentAction($action, route('search', ['query' => $doc->filename])));
                break;
            case Document::UNDER_FINALIZATION:
                $F_emails = User::query()->whereHas('roles', function ($query) {
                    $query->where('role_slug', 'finalizer');
                })->first();
                if ($F_emails != null)
                    Mail::to($F_emails->email)->send(new DocumentAction($action, route('search', ['query' => $doc->filename])));
                break;


            case Document::APPROVED || Document::REJECTED:
                $U_emails = User::query()->whereHas('roles', function ($query) {
                    $query->where('role_slug', 'uploader');
                })->first();
                if ($U_emails != null)
                    Mail::to($U_emails->email)->send(new DocumentAction($action, route('search', ['query' => $doc->filename])));
                break;
        }
    }

}
