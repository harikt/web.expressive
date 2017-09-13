<!DOCTYPE html>
<?php /** @var \Dms\Core\Auth\IAdmin $user */ ?>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="csrf-token" content="" />

    <title>{{ ($pageTitle ?? false) ? $pageTitle . ' :: ' : '' }}{{ $title }}</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    @include('dms::partials.assets.styles')
</head>
@yield('body-content')

@include('dms::partials.assets.scripts')
@include('dms::partials.js-config')
</html>
