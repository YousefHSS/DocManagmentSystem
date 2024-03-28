<?php

dataset('roles', function () {
    //    get all roles except the one to ignore
    $roles = ['admin', 'reviewer', 'finalizer', 'uploader'];

    return $roles;
});
