@extends('admin.adminLayout.master')
@section('styles')
<link rel="stylesheet" href="{{ asset('assets/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
<link rel="stylesheet" type="text/css" href="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.css">

<style>
    div.scroll {
        margin: 4px, 4px;
        padding: 4px;
        overflow: auto;
        white-space: nowrap;
    }
</style>
@endsection

@section('content')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Product</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Product</li>
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

            <!-- @if(session()->has('success'))
            <div class="alert alert-success">
                {{ session()->get('success') }}
            </div>
            @endif -->
            <div class="row">
                <div class="col-12">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Product List</h3>
                            <div class="card-tools">
                                <a type="button" href="{{ url('admin/product-create') }}" class="btn btn-secondary">
                                    <i class="nav-icon fas fa-plus"></i>
                                    Add Product
                                </a>
                            </div>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body scroll">
                            <table id="example2" class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>Sr. No.</th>
                                        <th>Category</th>
                                        <th>Product</th>
                                        <th>Code</th>
                                        <th>Price</th>
                                        <th>Promo Code</th>
                                        <th>Image</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($productData as $key => $product)
                                    <?php
                                        if(empty($product['product_category'])){
                                            $categoryName = "No Category";
                                        }else{
                                            $categoryName = $product['product_category']['category_name'];
                                        }
                                    ?>
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $categoryName }}</td>
                                        <td>{{ $product['product_name'] }}</td>
                                        <td>{{ $product['product_code'] }}</td>
                                        <td>{{ $product['product_price'] }}</td>
                                        <td>{{ $product['product_coupon']['coupon_code'] }}</td>
                                        <td>@if(!empty($product['product_image']))
                                            <img src="{{ asset('storage/'. $product['product_image']) }}" style="width: 150px; height:100px;" />
                                            @else
                                            <img src="{{ asset('uploads/cms/cms-dummy-image.png') }}" style="width: 150px; height:100px;" />
                                            @endif
                                        </td>
                                        <td>@if($product['status'] == 1) Active @else Inactive @endif</td>
                                        <td>
                                            <!-- <a href="" class="btn btn-success"><i class="fas fa-eye"></i></a> -->
                                            <a href="{{ url('admin/product-update/'.$product['slug'] .'/'. $product['id']) }}" class="btn btn-primary"><i class="fas fa-edit"></i></a>
                                            <!-- <a href="{{ url('admin/delete/'.$product['slug'] .'/'. $product['id']) }}" class="btn btn-danger"><i class="fas fa-trash"></i></a> -->
                                            <a href="javascript:void(0);" class="btn btn-danger confirmDelete" module="product" slug="{{ $product['slug'] }}" moduleId="{{ $product['id'] }}"><i class="fas fa-trash"></i></a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th>Sr. No.</th>
                                        <th>Category</th>
                                        <th>Product</th>
                                        <th>Code</th>
                                        <th>Price</th>
                                        <th>Promo Code</th>
                                        <th>Image</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        <!-- /.card-body -->
                    </div>
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->
        </div>
        <!-- /.container-fluid -->
    </section>
    <!-- /.content -->
</div>
@endsection

@section('scripts')
<!-- DataTables  & Plugins -->
<script src="{{ asset('assets/plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('assets/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('assets/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
<script src="{{ asset('assets/plugins/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
<script src="{{ asset('assets/plugins/datatables-buttons/js/buttons.bootstrap4.min.js') }}"></script>
<script src="{{ asset('assets/plugins/jszip/jszip.min.js') }}"></script>
<script src="{{ asset('assets/plugins/pdfmake/pdfmake.min.js') }}"></script>
<script src="{{ asset('assets/plugins/pdfmake/vfs_fonts.js') }}"></script>
<script src="{{ asset('assets/plugins/datatables-buttons/js/buttons.html5.min.js') }}"></script>
<script src="{{ asset('assets/plugins/datatables-buttons/js/buttons.print.min.js') }}"></script>
<script src="{{ asset('assets/plugins/datatables-buttons/js/buttons.colVis.min.js') }}"></script>
<script src="{{ asset('assets/custom.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endsection
