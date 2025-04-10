@extends('front.frontLayout.master')
@section('content')
<div class="row">

    @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif
    <div class="col-sm-4 pl-5">
        <form id="quickFormInvite" action="{{ route('invite') }}" method="Post">
            {{ csrf_field() }}

            <div class="input-group mb-3">
                <input type="text" class="form-control" id="name" name="name" value="{{ Auth::user()->name }}" placeholder="Your Email" readonly>
                <div class="input-group-append">
                    <div class="input-group-text">
                        <span class="fas fa-user"></span>
                    </div>
                </div>
            </div>

            <div class="input-group mb-3">
                <input type="email" class="form-control" id="fromEmail" name="fromEmail" value="{{ Auth::user()->email }}" placeholder="Your Email" readonly>
                <div class="input-group-append">
                    <div class="input-group-text">
                        <span class="fas fa-envelope"></span>
                    </div>
                </div>
            </div>
            <div class="input-group mb-3">
                <input type="email" class="form-control" id="toEmail" name="toEmail" placeholder="To Email">
                <div class="input-group-append">
                    <div class="input-group-text">
                        <span class="fas fa-envelope"></span>
                    </div>
                </div>
            </div>

            <div class="input-group mb-3">
                <input type="text" class="form-control" id="referal_code" name="referal_code" value="{{ Auth::user()->referal_code }}" placeholder="Your Referal Code">
                <div class="input-group-append">
                    <div class="input-group-text">
                        <span class="fas fa-file-code"></span>
                    </div>
                </div>
            </div>

            <div class="input-group mb-3">
                <input type="text" class="form-control" id="link" name="link" value="{{ $link }}" placeholder="Your Referal Link">
                <div class="input-group-append">
                    <div class="input-group-text">
                        <span class="fas fa-file-code"></span>
                    </div>
                </div>
            </div>

            <div class="button">
                <button type="submit" class="btn btn-success">Send</a>
            </div>
        </form>
    </div>
</div>
@endsection