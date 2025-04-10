@extends('front.frontLayout.master')

@section('styles')
<style>
    .card-primary.card-outline {
        background: transparent;
        border-top: none;
        box-shadow: none;
    }

    a {
        text-decoration: none;
        color: black;
        font-weight: 600;
    }

    .list-group-item {
        background: transparent;
    }
</style>
@endsection

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

    @if (Session::has('success'))
    <div class="alert alert-success" style="height:50px; border-radius:20px;">
        <a href="#" class="close" data-dismiss="alert" aria-label="close" style="text-decoration: none; height:50px; border-radius:0.75px;">x</a>
        <p>{{ Session::get('success') }}</p>
    </div>
    @endif

    @if (Session::has('error'))
    <div class="alert alert-danger" style="height:50px; border-radius:20px;">
        <strong>{{ Session::get('error') }}</strong>
        <a href="#" class="close" data-dismiss="alert" aria-label="close" style="text-decoration: none;">x</a>
    </div>
    @endif
    <div class="col-md-3">
        <!-- Profile Image -->
        <div class="card card-primary card-outline">
            <div class="card-body box-profile">
                <div class="text-center">
                    @if(Auth::user()->avatar)
                    <img class="profile-user-img img-fluid img-circle" src="{{ asset('/storage/'.Auth::user()->avatar) }}" id="user_img" alt="User profile picture">
                    @else
                    <img class="profile-user-img img-fluid img-circle" src="{{ asset('assets/dist/img/user4-128x128.jpg') }}" id="user_img" alt="User profile picture">
                    @endif
                </div>

                <h3 class="profile-username text-center">{{ Auth::user()->name }}</h3>

                <!-- <p class="text-muted text-center">Software Engineer</p> -->

                <ul class="list-group list-group-unbordered mb-3">
                    <li class="list-group-item">
                        <b>Name</b> <a class="float-right">{{ Auth::user()->name }}</a>
                    </li>
                    <li class="list-group-item">
                        <b>Email</b> <a class="float-right">{{ Auth::user()->email }}</a>
                    </li>
                    <li class="list-group-item">
                        <b>Mobile</b> <a class="float-right">{{ Auth::user()->mobile }}</a>
                    </li>
                </ul>

                <a href="{{ route('listing') }}" class="btn btn-primary btn-block"><b>Home</b></a>
            </div>
            <!-- /.card-body -->
        </div>
        <!-- /.card -->
        <!-- /.card -->
    </div>
    <!-- /.col -->

    <div class="col-md-9">
        <div class="card">
            <div class="card-header p-2">
                <ul class="nav nav-pills">
                    <li class="nav-item"><a class="nav-link active">Update Profile</a></li>
                </ul>
            </div><!-- /.card-header -->
            <div class="card-body">
                <div class="tab-content">
                    <div class="active tab-pane" id="settings">
                        <form class="form-horizontal" action="{{ route('profile') }}" method="post" enctype="multipart/form-data">
                            {{ csrf_field() }}
                            <div class="form-group row">
                                <label for="inputName" class="col-sm-2 col-form-label">Name</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" name="name" id="name" placeholder="Name" value="{{ Auth::user()->name }}">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="inputEmail" class="col-sm-2 col-form-label">Email</label>
                                <div class="col-sm-10">
                                    <input type="email" class="form-control" name="email" id="email" value="{{ Auth::user()->email }}" placeholder="Email" readonly>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="inputName2" class="col-sm-2 col-form-label">Mobile</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" name="mobile" id="mobile" placeholder="Mobile" value="{{ Auth::user()->mobile }}">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="inputSkills" class="col-sm-2 col-form-label">Profile Picture</label>
                                <div class="col-sm-10">
                                    <input type="file" class="form-control" name="avatar" id="avatar" onchange="show(this)">
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="offset-sm-2 col-sm-10">
                                    <button type="submit" class="btn btn-danger">Submit</button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <!-- /.tab-pane -->
                </div>
                <!-- /.tab-content -->
            </div><!-- /.card-body -->
        </div>
        <!-- /.card -->
    </div>
    <!-- /.col -->
</div>
<!-- <div class="row">

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
        <form id="quickFormProfile" action="{{ route('profile') }}" method="Post">
            {{ csrf_field() }}

            <div class="input-group mb-3">
                <input type="text" class="form-control" id="name" name="name" value="{{ Auth::user()->name }}" placeholder="Your Name">
                <div class="input-group-append">
                    <div class="input-group-text">
                        <span class="fas fa-user"></span>
                    </div>
                </div>
            </div>

            <div class="input-group mb-3">
                <input type="email" class="form-control" id="email" name="email" value="{{ Auth::user()->email }}" placeholder="Your Email" readonly>
                <div class="input-group-append">
                    <div class="input-group-text">
                        <span class="fas fa-envelope"></span>
                    </div>
                </div>
            </div>

            <div class="input-group mb-3">
                <input type="text" class="form-control" id="mobile" name="mobile" value="{{ Auth::user()->mobile }}" placeholder="Your Phone Number">
                <div class="input-group-append">
                    <div class="input-group-text">
                        <span class="fas fa-file-code"></span>
                    </div>
                </div>
            </div>

            <div class="input-group mb-3">
                <input type="file" class="form-control" id="avatar" name="avatar">
                <div class="input-group-append">
                    <div class="input-group-text">
                        <span class="fas fa-file-code"></span>
                    </div>
                </div>
            </div>

            <div class="button">
                <button type="submit" class="btn btn-success">Submit</a>
            </div>
        </form>
    </div>
</div> -->
@endsection

@section('scripts')
<SCRIPT type="text/javascript">
    function show(input) {
        debugger;
        var validExtensions = ['jpg', 'png', 'jpeg']; //array of valid extensions
        var fileName = input.files[0].name;
        var fileNameExt = fileName.substr(fileName.lastIndexOf('.') + 1);
        if ($.inArray(fileNameExt, validExtensions) == -1) {
            input.type = ''
            input.type = 'file'
            $('#user_img').attr('src', "");

            Swal.fire({
                title: "Invalid Extension!",
                text: "Only these file types are accepted : " + validExtensions.join(', '),
                icon: "error"
            });

            alert("Only these file types are accepted : " + validExtensions.join(', '));
        } else {
            if (input.files && input.files[0]) {
                var filerdr = new FileReader();
                filerdr.onload = function(e) {
                    $('#user_img').attr('src', e.target.result);
                }
                filerdr.readAsDataURL(input.files[0]);
            }
        }
    }
</SCRIPT>
@endsection
