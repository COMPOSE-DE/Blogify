@extends('blogifyPublic::templates.master')
@section('content')

    <?php $data = session()->get('notify') ?>
    <div id="notify" class="fixed-to-top">
        @include('blogify::admin.widgets.alert', ['class'=>$data[0], 'dismissable'=>true, 'message'=> $data[1], 'icon'=> 'check'])
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-heading"><a href="{{route('blog.show', [$post->slug])}}">{{$post->title}}</a></div>
                        <div class="panel-body">
                            {!! $post->content !!}
                            <p>
                                <small><em>Posted in <strong>{{$post->category->name}}</strong></em></small>
                            </p>
                            <p>
                                <small><strong>Tags:</strong></small>
                            </p>
                            <p>
                            <div id="tags">
                                @if( count($post->tag) > 0 )
                                    @foreach ( $post->tag as $tag )
                                        <span class="tag {{$tag->hash}}"><a href="#" class="{{$tag->hash}}" title="Remove tag"><span class="fa fa-times-circle"></span></a> {{ $tag->name }} </span>
                                    @endforeach
                                @endif
                            </div>
                            </p>
                        </div>
                        <div class="panel-footer">
                            <div class="row">
                                <div class="col-md-6 col-xs-12">
                                    <?php $number_of_comments = 0; ?>
                                    @foreach($post->comment as $comment)
                                        @if($comment->revised == 2)
                                            <?php $number_of_comments++; ?>
                                        @endif
                                    @endforeach
                                    <small><a href="{{route('blog.show', [$post->slug])}}">{{$number_of_comments}} comments</a></small>
                                </div>
                                <div class="col-md-6 col-xs-12 text-right">
                                    <small>Posted on {{$post->publish_date}} by {{$post->user->fullName}}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <h3>Comments</h3>

            {!! Form::open( [ 'route' => 'comments.store' ] ) !!}

            <div class="row form-group {{ $errors->has('comment') ? 'has-error' : '' }}">
                <div class="col-sm-12">
                    {!! Form::textarea('comment', '', ['class' => 'form-control', 'rows' => 5 ]) !!}
                    {!! Form::hidden('post', $post->hash) !!}
                </div>
            </div>

            <div class="row">
                <div class="col-sm-12">
                    {!! Form::submit('Add comment', ['class'=>'btn btn-success']) !!}
                </div>
            </div>

            {!! Form::close() !!}

            <hr>

            @foreach($post->comment as $comment)
                <div class="media">
                    <div class="media-left">
                        <a href="#">
                            <img class="media-object" src="{{URL::asset($comment->user->profilepicture)}}" alt="..." width="64px" height="64px">
                        </a>
                    </div>
                    <div class="media-body">
                        <span class="media-heading"><em>{{$comment->user->fullName}} posted on {{$comment->created_at}}</em></span>
                        <p>
                            {!!nl2br($comment->content)!!}
                        </p>
                    </div>
                </div>
            @endforeach

            <hr>

        </div>
    </div>
@stop