@extends('errors.minimal')

@section('title', __('Accès non autorisé'))
@section('code', '403')
@section('message', __($exception->getMessage() ?: 'Accès non autorisé'))
