@extends('errors::minimal')

@section('title', __('Service Unavailable'))
@section('code', '503')
@section('message', __('Service Unavailable'))
@section('description', 'We are currently performing maintenance. Please check back in a few minutes.')
