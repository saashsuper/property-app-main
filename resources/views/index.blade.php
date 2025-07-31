@extends('layouts.master')
@section('title')
    @lang('translation.starter')
@endsection
@section('css')
    <!-- add your css here -->
@endsection
@section('content')
    <!-- start your content here -->

@endsection

@section('script')
    <!-- add your js here -->
    <script src="{{ URL::asset('build/js/app.js') }}"></script>
@endsection
