<?php
$trashed        = ($trashed) ? 1 : 0;
$currentPage    = (Request::has('page')) ? Request::get('page') : '1';
?>
@extends('blogify::admin.layouts.dashboard')
@section('page_heading', trans("blogify::users.overview.page_title") )
@section('section')
    @if ( session()->get('notify') )
        @include('blogify::admin.snippets.notify')
    @endif
    @if ( session()->has('success') )
        @include('blogify::admin.widgets.alert', ['class'=>'success', 'dismissable'=>true, 'message'=> session()->get('success'), 'icon'=> 'check'])
    @endif

    <p>
        <a href="{{ ($trashed) ? route('admin.users.index') : route('admin.users.overview', ['trashed']) }}" title=""> {{ ($trashed) ? trans('blogify::users.overview.links.active') : trans('blogify::users.overview.links.trashed') }} </a>
    </p>

@section ('cotable_panel_title', ($trashed) ? trans("blogify::users.overview.table_head.title_trashed") : trans("blogify::users.overview.table_head.title_active"))
@section ('cotable_panel_body')
    <table class="table table-bordered sortable">
        <thead>
        <tr>
            <th role="name"><a href="{!! route('admin.api.sort', ['users', 'name', 'asc', $trashed]).'?page='.$currentPage !!}" title="Order by Name" class="sort"> {{ trans("blogify::users.overview.table_head.name") }} </a></th>
            <th role="email"><a href="{!! route('admin.api.sort', ['users', 'email', 'asc', $trashed]).'?page='.$currentPage !!}" title="Order by E-mail" class="sort"> {{ trans("blogify::users.overview.table_head.email") }} </a></th>
            <th role="roles">{{ trans("blogify::users.overview.table_head.roles") }}</th>
            <th> {{ trans("blogify::users.overview.table_head.actions") }} </th>
        </tr>
        </thead>
        <tbody>
        @if ( count($users) <= 0 )
            <tr>
                <td colspan="7">
                    <em>@lang('blogify::users.overview.no_results')</em>
                </td>
            </tr>
        @endif

        @foreach ( $users as $user )
            <tr>
                <td>{!! $user->name !!}</td>
                <td>{!! $user->email !!}</td>
                <td>{!! $user->roles->sortBy(function($role){ return array_search($role->name, BlogifyRole::getRoleOrder()); })->implode('name', ', ') !!}</td>
                <td>
                    @if(!$trashed)
                        <a href="{{ route('admin.users.edit', [$user->id] ) }}"><span class="fa fa-edit fa-fw"></span></a>
                        {!! Form::open( [ 'route' => ['admin.users.destroy', $user->id], 'class' => $user->id . ' form-delete' ] ) !!}

                        {!! Form::hidden('_method', 'delete') !!}
                        <a href="#" title="{{$user->name}}" class="delete" id="{{$user->id}}"><span class="fa fa-trash-o fa-fw"></span></a>
                        {!! Form::close() !!}
                    @else
                        <a href="{{route('admin.users.restore', [$user->id])}}" title="">Restore</a>
                    @endif
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endsection

@include('blogify::admin.widgets.panel', ['header'=>true, 'as'=>'cotable'])

{!! $users->render() !!}

@stop
