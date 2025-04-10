@extends('admin.adminLayout.master')

@section('styles')
<link rel="stylesheet" href="{{ asset('assets/passtrength.css') }}">
@endsection

@section('content')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>User</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="">Home</a></li>
                        <li class="breadcrumb-item active">User</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif
            <form id="quickForm" method="POST" action="">
                @csrf
                <div class="mb-3">
                    <label for="name" class="form-label">Name</label>
                    <input value="{{ old('name') }}" type="text" class="form-control" name="name" placeholder="Name" required>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input value="{{ old('email') }}" type="text" class="form-control" name="email" id="email" required>
                </div>

                <div class="mb-3">
                    <label for="username" class="form-label">User Name</label>
                    <input value="{{ old('username') }}" type="text" class="form-control" name="username" id="username" placeholder="User Name" required>
                </div>

                <div class="row">
                    <div class="col-sm-6 mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input value="{{ old('username') }}" type="password" class="form-control" name="password" id="myPassword" placeholder="Password" required>
                    </div>
    
                    <div class="col-sm-6" style="margin-top: 2rem;">
                    <button type="button" class="btn btn-success" onclick="generatePass()">Generate</button>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="type" class="form-label">User Type</label>
                    <input value="{{ old('type') }}" type="text" class="form-control" name="type" placeholder="Name" required>
                </div>

                <button type="submit" class="btn btn-primary">Save user</button>
                <a href="{{ route('admin.user.index') }}" class="btn btn-default">Back</a>
            </form>
        </div>
    </section>
    <!-- /.content -->
</div>
@endsection

@section('scripts')

<script src="https://cdn.ckeditor.com/ckeditor5/35.1.0/classic/ckeditor.js"></script>
<script src="{{ asset('assets/plugins/jquery-validation/jquery.validate.min.js') }}"></script>
<script src="{{ asset('assets/plugins/jquery-validation/additional-methods.min.js') }}"></script>
<script src="{{ asset('assets/passtrength.js') }}"></script>

<script>
    
    $("#email").on('change', function() {
        var email = document.getElementById("email").value;
        var username = email.substring(0, email.indexOf('@'));
        document.getElementById('username').value = username;
    });

    function generatePass() {
        let pass = '';
        let str = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ' +
            'abcdefghijklmnopqrstuvwxyz0123456789@#$%!';

        for (let i = 1; i <= 8; i++) {
            let char = Math.floor(Math.random() *
                str.length + 1);

            pass += str.charAt(char)
        }
        
        document.getElementById('myPassword').value = pass;

    }

    $(document).ready(function($) {
        $('#myPassword').passtrength({
            minChars: 4,
            passwordToggle: true,
            tooltip: true,
            eyeImg: "/assets/eye.svg"
        });
    });

    $(function() {
        $('#quickForm').validate({
            rules: {
                name: {
                    required: true,
                },
                email: {
                    required: true,
                    email: true
                }
            },
            messages: {
                name: {
                    required: "Please Enter Name",
                },
                email: {
                    required: "Please enter Email",
                    email: "Please enter a valid email"
                }
            },
            errorElement: 'span',
            errorPlacement: function(error, element) {
                error.addClass('invalid-feedback');
                element.closest('.form-group').append(error);
            },
            highlight: function(element, errorClass, validClass) {
                $(element).addClass('is-invalid');
            },
            unhighlight: function(element, errorClass, validClass) {
                $(element).removeClass('is-invalid');
            }
        });
    });
</script>
@endsection