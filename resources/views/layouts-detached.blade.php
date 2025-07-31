@extends('layouts.layouts-detached')
@section('title')
    @lang('translation.starter')
@endsection
@section('css')
    <!-- add your css here -->
@endsection
@section('content')
@component('components.breadcrumb')
@slot('li_1') Layouts @endslot
@slot('title') Detached @endslot
@endcomponent
    <!-- start your content here -->

@endsection

@section('script')
    <!-- add your js here -->
    <script src="{{ URL::asset('build/js/app.js') }}"></script>
@endsection
