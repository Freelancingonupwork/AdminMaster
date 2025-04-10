@extends('admin.adminLayout.master')
@section('content')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Coupon</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="">Home</a></li>
                        <li class="breadcrumb-item active">Coupon</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
        <form @if(!isset($coupon) || empty($coupon)) action="{{ url('admin/coupon-create') }}" @else acion="{{ url('admin/coupon-update/'.$coupon['slug']) }}" @endif method="post" enctype="multipart/form-data">
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
                            <h3 class="card-title">Add Coupon</h3>
                            <div class="card-tools">
                                <a type="button" class="btn btn-secondary" href="{{ url('admin/coupon-index') }}">
                                    <i class="fas fa-arrow-left"></i>
                                    Return to List
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label for="inputName">Coupon Option</label><br>
                                <input type="radio" id="automatic" name="coupon_option" value="automatic" @if(isset($coupon) && $coupon['coupon_option'] == "automatic") checked @endif>
                                <label for="coupon_option">Automatic</label>&emsp;
                                <input type="radio" id="manual" name="coupon_option" value="manual" @if(isset($coupon) && $coupon['coupon_option'] == "manual") checked @endif>
                                <label for="coupon_option">Manually</label>
                                <!-- <input type="text" id="coupon_option" name="coupon_option" class="form-control" @if(isset($coupon)) value="{{ $coupon->coupon_code }}" @else value="{{ old('coupon_code') }}" @endif> -->
                            </div>
                            <div class="form-group coupon_code">
                                <label for="inputName">Coupon Code</label>
                                <input type="text" id="coupon_code" name="coupon_code" class="form-control" @if(isset($coupon)) value="{{ $coupon->coupon_code }}" @else value="{{ old('coupon_code') }}" @endif>
                            </div>
                            <div class="form-group">
                                <label for="inputName">Amount Type</label><br>
                                <input type="radio" id="percent" name="amount_type" value="percent" @if(isset($coupon) && $coupon['amount_type'] == "percent") checked @endif>
                                <label for="amount_type">Percent</label>&emsp;
                                <input type="radio" id="fixed" name="amount_type" value="fixed" @if(isset($coupon) && $coupon['amount_type'] == "fixed") checked @endif>
                                <label for="amount_type">Fixed</label>
                            </div>
                            <div class="form-group">
                                <label for="inputName">Amount</label>
                                <input type="number" id="amount" name="amount" class="form-control" @if(isset($coupon)) value="{{ $coupon->amount }}" @else value="{{ old('amount') }}" @endif>
                            </div>
                            <div class="form-group">
                                <label for="inputName">Expiry Date</label>
                                <input type="date" id="expiry_date" name="expiry_date" class="form-control" @if(isset($coupon)) value="{{ $coupon->expiry_date }}" @else value="{{ old('expiry_date') }}" @endif>
                            </div>
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="status" name="status" @if(isset($coupon) && $coupon['status'] == 1) checked @endif>
                                <label for="status">Enable</label>
                            </div>
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                </div>
            </div>
            <div class="row">
                <div class="col-12 mb-2">
                    <input type="submit" @if(!isset($coupon)) value="Save" @else value="Update" @endif class="btn btn-success float-right">
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

<script>
    $(document).ready(function(){
    $('input[name$="coupon_option"]').click(function(){
        var inputValue = $(this).attr("value");
        if(inputValue == "automatic"){
            $(".coupon_code").hide();
        }else{

            $(".coupon_code").show();
        }
    });
});
</script>

@endsection