@extends('layouts.app')

@section('title')
    {{ $title='Dashboard' }}
@stop

{{--variable slot--}}
@section('content')
{{--  a simple users table with ability to change role--}}
    <h1>Dashboard</h1>
    <table class="table table-bordered m-10">
        <thead>
        <tr>
            <th>Username</th>
            <th>Email</th>
            <th>Role</th>
            <th>Change Role</th>
        </tr>
        </thead>
        <tbody>
        @foreach($users as $user)
            <tr>
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>

                <td>{{ $user->getRole() ? $user->getRole()->role_name : "No Role" }}</td>
                <td>
                    <form action="{{ route('change.role') }}" method="post">
                        {{ csrf_field() }}
                        <input type="hidden" name="user_id" value="{{ $user->id }}">
                        <select name="role" id="role" class="form-control m-1">
            @foreach( $roles as $role)
                <option value="{{ $role->role_slug }}">{{ $role->role_name }}</option>
            @endforeach
                        </select>
                        <button type="submit" class="btn btn-primary m-1">Change</button>
                    </form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
@stop
