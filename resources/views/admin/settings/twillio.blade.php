@extends('admin.adminLayout.master')

@section('styles')
<link rel="stylesheet" type="text/css" href="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.css">
@endsection

@section('content')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Twillio Setting</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="">Home</a></li>
                        <li class="breadcrumb-item active">Settings</li>
                        <li class="breadcrumb-item active">Twillio</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
        <form action="" method="post" enctype="multipart/form-data">
            {{ csrf_field() }}
            @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Twillio Setting</h3>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <label for="twilio_account_sid" class="col-sm-2 col-form-label">Twilio Account SID</label>
                                <div class="col-sm-10">
                                    <input type="text" name="twilio_account_sid" id="twilio_account_sid" class="form-control" value="{{ $settings->getSetting('twilio_account_sid') }}">
                                    <div class="valid-feedback"></div>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label for="twilio_auth_token" class="col-sm-2 col-form-label">Twilio Auth Token</label>
                                <div class="col-sm-10">
                                    <input type="text" name="twilio_auth_token" id="twilio_auth_token" class="form-control" value="{{ $settings->getSetting('twilio_auth_token') }}">
                                    <div class="valid-feedback"></div>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label for="twilio_number" class="col-sm-2 col-form-label">Twilio Phone number</label>
                                <div class="col-sm-10">
                                    <input type="text" name="twilio_number" id="twilio_number" class="form-control" value="{{ $settings->getSetting('twilio_number') }}">
                                    <div class="valid-feedback"></div>
                                </div>
                            </div>
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                </div>
            </div>
            <div class="row">
                <div class="col-12 mb-2">
                    <input type="submit" value="Save" class="btn btn-success float-right">
                </div>
            </div>
        </form>
    </section>
    <!-- /.content -->
</div>
@endsection

@section('scripts')

<script src="https://cdn.ckeditor.com/ckeditor5/35.1.0/classic/ckeditor.js"></script>
<script>
    ClassicEditor
        .create(document.querySelector('#description'))
        .catch(error => {
            console.error(error);
        });
</script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
<script>
    @if(Session::has('success'))
    toastr.options = {
        "closeButton": true,
        "progressBar": true
    }
    toastr.success("{{ session('success') }}");
    @endif
</script>

@endsection