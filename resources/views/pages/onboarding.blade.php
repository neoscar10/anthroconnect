@extends('layouts.public', ['noManualAlpine' => true])

@section('content')
    <livewire:onboarding.step-page :step-slug="$stepSlug ?? ''" />
@endsection
