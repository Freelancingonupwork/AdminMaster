<?php

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
?>
<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="{{ route('admin.dashboard') }}" class="brand-link">
        <img src="{{ asset('assets/dist/img/AdminLTELogo.png') }}" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
        <span class="brand-text font-weight-light">Admin</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                @if(empty(Auth::user()->avatar))
                <img src="{{ asset('assets/dist/img/user2-160x160.jpg') }}" class="img-circle elevation-2" alt="User Image">
                @else
                <img src="{{ asset('/storage/'.Auth::user()->avatar) }}" class="img-circle elevation-2" alt="User Image" style="width:35px; height: 35px;">
                @endif
            </div>
            <div class="info">
                <h6 class="d-block" style="color:whitesmoke">{{ Auth::user()->name }}</h6>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
                <li class="nav-item menu-open">
                    <a href="{{ url('admin/dashboard') }}" class="nav-link active">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>
                            Dashboard
                            <!-- <i class="right fas fa-angle-left"></i> -->
                        </p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('admin.category-index') }}" class="nav-link">
                        <i class="nav-icon fas fa-list"></i>
                        <p>
                            Category
                        </p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('admin.product-index') }}" class="nav-link">
                        <i class="nav-icon fa-brands fa-product-hunt"></i>
                        <p>
                            Product
                        </p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('admin.coupon-index') }}" class="nav-link">
                        <i class="nav-icon fa-solid fa-money-bill"></i>
                        <p>
                            Coupon
                        </p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('admin.cms-index') }}" class="nav-link">
                        <i class="nav-icon fas fa-list"></i>
                        <p>
                            CMS
                        </p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('admin.plan-index') }}" class="nav-link">
                        <i class="fa-solid fa-clipboard-list nav-icon"></i>
                        <p>
                            Plans
                        </p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fa-solid fa-gear"></i>
                        <p>
                            Settings
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ url('admin/setting-email') }}" class="nav-link">
                                <i class="far fa-envelope nav-icon"></i>
                                <p>Email</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ url('admin/setting-twillio') }}" class="nav-link">
                                <i class="far fa-message nav-icon"></i>
                                <p>Twillio</p>
                            </a>
                        </li>
                        @auth
                        @role('superadmin')

                        <li class="nav-item">
                            <a href="{{ url('admin/user-index') }}" class="nav-link">
                                <i class="fa-solid fa-users nav-icon"></i>
                                <p>Users</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{ route('roles.index') }}" class="nav-link">
                                <i class="fa-solid fa-gears nav-icon"></i>
                                <p>Roles</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{ route('permissions.index') }}" class="nav-link">
                                <img class="nav-icon" src="{{ asset('/icons/set-permission.png') }}" style="size: 10px; color: white;">
                                <p>Permissions</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{ route('stripe-index') }}" class="nav-link">
                                <img class="nav-icon" src="{{ asset('/icons/set-permission.png') }}" style="size: 10px; color: white;">
                                <p>Stripe</p>
                            </a>
                        </li>
                        @endrole
                        @endauth
                    </ul>
                </li>

                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fa-solid fa-group-arrows-rotate"></i>
                        <p>
                            Community
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ url('admin/discussion-index') }}" class="nav-link">
                                <i class="nav-icon fa-regular fa-comments"></i>
                                <p>Discussion</p>
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>
