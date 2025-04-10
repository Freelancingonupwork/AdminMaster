@extends('admin.adminLayout.master')
@section('content')

<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <!-- <h1>Category</h1> -->
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <!-- <li class="breadcrumb-item"><a href="">Home</a></li>
                        <li class="breadcrumb-item active">Category</li> -->
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Not Allowed</h3>
                            <div class="card-tools">
                                <a type="button" href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
                                    <i class="nav-icon fas fa-tachometer-alt"></i>
                                    Dashboard
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <h1 style="text-align: center;">You do not have the permissions.</h1>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- /.content -->
</div>

@endsection