<!DOCTYPE html>
<html lang="en">

<head>
  {{-- AOS --}}
  <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
  <!-- Tables -->
  <link rel="stylesheet" href="https://cdn.datatables.net/2.3.0/css/dataTables.dataTables.css" />
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Toko Sembako</title>
  <!-- plugins:css -->
  <link rel="stylesheet" href="{{asset('template/')}}/assets/vendors/mdi/css/materialdesignicons.min.css">
  <link rel="stylesheet" href="{{asset('template/')}}/assets/vendors/ti-icons/css/themify-icons.css">
  <link rel="stylesheet" href="{{asset('template/')}}/assets/vendors/css/vendor.bundle.base.css">
  <link rel="stylesheet" href="{{asset('template/')}}/assets/vendors/font-awesome/css/font-awesome.min.css">
  <!-- endinject -->
  <!-- Plugin css for this page -->
  <link rel="stylesheet" href="{{asset('template/')}}/assets/vendors/font-awesome/css/font-awesome.min.css" />
  <link rel="stylesheet" href="{{asset('template/')}}/assets/vendors/bootstrap-datepicker/bootstrap-datepicker.min.css">
  <!-- End plugin css for this page -->
  <!-- inject:css -->
  <!-- endinject -->
  <!-- Layout styles -->
  <link rel="stylesheet" href="{{asset('template/')}}/assets/css/style.css">
  <!-- End layout styles -->
  <link rel="shortcut icon" href="{{asset('template/')}}/assets/images/favicon.png" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
</head>

<body>
  <!-- partial:partials/_navbar.html -->
  <nav class="navbar default-layout-navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row">
    <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-start">
      <a class="navbar-brand brand-logo" style="color: blueviolet" href="index.html">
        <h4 class="font-weight-bold">Toko Sembako Lina</h4>
      </a>
      <a class="navbar-brand brand-logo-mini" href="/home"><img src="{{asset('template/')}}/assets/images/logo-mini.svg"
          alt="logo" /></a>
    </div>
    <div class="navbar-menu-wrapper d-flex align-items-stretch">
      <ul class="navbar-nav navbar-nav-right">
        <li class="nav-item nav-profile dropdown">
          <a class="nav-link dropdown-toggle" id="profileDropdown" href="#" data-bs-toggle="dropdown"
            aria-expanded="false">
            <div class="nav-profile-img">
              <img src="{{asset('template/')}}/assets/images/faces/owner_2.png" alt="image">
              <span class="availability-status online"></span>
            </div>
            <div class="nav-profile-text">
              <p class="mb-1 text-black"> {{ Auth::user()->name ?? 'Admin' }}</p>
            </div>
          </a>
          <div class="dropdown-menu navbar-dropdown" aria-labelledby="profileDropdown">
            <div class="dropdown-divider"></div>
            <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
              {{ __('Logout') }}
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
              @csrf
            </form>
          </div>
        </li>
      </ul>
      <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button"
        data-toggle="offcanvas">
        <span class="mdi mdi-menu"></span>
      </button>
    </div>
  </nav>
  <!-- partial -->
  <div class="container-fluid page-body-wrapper">
    <!-- partial:partials/_sidebar.html -->
    <nav class="sidebar sidebar-offcanvas" id="sidebar">
      <ul class="nav">
        <li class="nav-item nav-profile ">
          <a href="#" class="nav-link">
            <div class="nav-profile-image">
              <img src="{{asset('template/')}}/assets/images/faces/owner_2.png" alt="profile" />
              <span class="login-status online"></span>
              <!--change to offline or busy as needed-->
            </div>
            <div class="nav-profile-text d-flex flex-column">
              <span class="font-weight-bold mb-2">{{ Auth::user()->name ?? 'Admin' }}</span>
              <span class="text-secondary text-small">Sebagai {{ Auth::user()->role->role ?? 'Menunggu Role' }}</span>
            </div>
            <i class="mdi mdi-bookmark-check text-success nav-profile-badge"></i>
          </a>
        </li>
        <li class="nav-item {{ Request::is('home', 'home/*') ? 'active-ref active' : '' }}">
          <a class="nav-link" href="/home">
            <span class="menu-title">Dashboard</span>
            <i class="mdi mdi-home menu-icon"></i>
          </a>
        </li>
        @if(Auth::user()->hasAnyRole(['owner', 'admin', 'kasir']))
        <li class="nav-item {{ Request::is('menu', 'menu/*') ? 'active-ref active' : '' }}">
          <a class="nav-link" href="/menu" aria-expanded="false" aria-controls="tables">
            <span class="menu-title">Menu</span>
            <i class="mdi mdi-silverware menu-icon"></i>
          </a>
        </li>
        @endif
        @if(Auth::user()->hasAnyRole(['owner', 'admin']))
        <li class="nav-item {{ Request::is('resep', 'resep/*') ? 'active-ref active' : '' }}">
          <a class="nav-link" href="/resep">
            <span class="menu-title">Resep Menu</span>
            <i class="mdi mdi-book-open-page-variant menu-icon"></i>
          </a>
        </li>
        @endif
        @if(Auth::user()->hasAnyRole(['owner', 'admin', 'kasir']))
          <li class="nav-item {{ Request::is('kasir', 'kasir/*') ? 'active-ref active' : '' }}">
            <a class="nav-link" href="/kasir" aria-expanded="false" aria-controls="pos">
              <span class="menu-title text-success font-weight-bold">Kasir (transaksi)</span>
              <i class="mdi mdi-calculator menu-icon text-success"></i>
            </a>
          </li>
        @endif
        @if(Auth::user()->hasAnyRole(['owner', 'admin', 'kasir']))
          <li class="nav-item {{ Request::is('riwayat-transaksi', 'riwayat-transaksi/*') ? 'active-ref active' : '' }}">
            <a class="nav-link" href="/riwayat-transaksi" aria-expanded="false" aria-controls="icons">
              <span class="menu-title">Riwayat Transaksi</span>
              <i class="mdi mdi-contacts menu-icon"></i>
            </a>
          </li>
        @endif
        @if(Auth::user()->hasAnyRole(['owner', 'admin']))
          <li class="nav-item {{ Request::is('stok', 'stok/*') ? 'active-ref active' : '' }}">
            <a class="nav-link" data-bs-toggle="collapse" href="#stok-menu" aria-expanded="{{ Request::is('stok', 'stok/*') ? 'true' : 'false' }}"
              aria-controls="stok-menu">
              <span class="menu-title">Manajemen Stok</span>
              <i class="menu-arrow"></i>
              <i class="mdi mdi-format-list-bulleted menu-icon"></i>
            </a>
            <div class="collapse {{ Request::is('stok', 'stok/*') ? 'show' : '' }}" id="stok-menu">
              <ul class="nav flex-column sub-menu">
                <li class="nav-item"> <a class="nav-link {{ Request::is('stok/add') ? 'active' : '' }}" href="/stok/add">Stok Masuk</a></li>
                <li class="nav-item"> <a class="nav-link {{ Request::is('stok/keluar', 'stok/keluar/*') ? 'active' : '' }}" href="/stok/keluar">Stok Keluar</a></li>
                <li class="nav-item"> <a class="nav-link {{ Request::is('stok/riwayat') ? 'active' : '' }}" href="/stok/riwayat">Riwayat Transaksi Stok</a></li>
                <li class="nav-item"> <a class="nav-link {{ Request::is('stok') ? 'active' : '' }}" href="/stok">Informasi Stok</a></li>
              </ul>
            </div>
          </li>
          <li class="nav-item {{ Request::is('laporan', 'laporan/*') ? 'active-ref active' : '' }}">
            <a class="nav-link" href="/laporan">
              <span class="menu-title">Laporan Penjualan</span>
              <i class="mdi mdi-format-list-bulleted menu-icon"></i>
            </a>
          </li>
          <li class="nav-item {{ Request::is('manajemen-user', 'manajemen-user/*') ? 'active-ref active' : '' }}">
            <a class="nav-link" href="/manajemen-user">
              <span class="menu-title">Manajemen User</span>
              <i class="mdi mdi-account-multiple menu-icon"></i>
            </a>
          </li>
        @endif
      </ul>
    </nav>
    <!-- partial -->
    <div class="main-panel">
      @yield('content')
    </div>
    <!-- main-panel ends -->
  </div>
  <!-- page-body-wrapper ends -->
  </div>

  <!-- container-scroller -->
  <!-- plugins:js -->
  <script src="{{asset('template/')}}/assets/vendors/js/vendor.bundle.base.js"></script>
  <!-- endinject -->
  <!-- Plugin js for this page -->
  <script src="{{asset('template/')}}/assets/vendors/chart.js/chart.umd.js"></script>
  <script src="{{asset('template/')}}/assets/vendors/bootstrap-datepicker/bootstrap-datepicker.min.js"></script>
  <!-- End plugin js for this page -->
  <!-- inject:js -->
  <script src="{{asset('template/')}}/assets/js/off-canvas.js"></script>
  <script src="{{asset('template/')}}/assets/js/misc.js"></script>
  <script src="{{asset('template/')}}/assets/js/settings.js"></script>
  <script src="{{asset('template/')}}/assets/js/todolist.js"></script>
  <script src="{{asset('template/')}}/assets/js/jquery.cookie.js"></script>
  <!-- endinject -->
  <!-- Custom js for this page -->
  <script src="{{asset('template/')}}/assets/js/dashboard.js"></script>
  <!-- End custom js for this page -->
  <script src="https://cdn.datatables.net/2.3.0/js/dataTables.js"></script>
  <!-- Tables -->
  <script>
    $(document).ready(function () {
      $('#mytable').DataTable();
    });
  </script>
  <script>
    window.onload = function () {
      var produkList = document.getElementById('produk-list');
      var addBtn = document.getElementById('add-produk');
      if (!produkList || !addBtn) return;
      addBtn.onclick = function (e) {
        e.preventDefault();
        var row = produkList.querySelector('.produk-row');
        if (!row) return;
        var clone = row.cloneNode(true);
        // Reset all input/select in the new row
        var selects = clone.querySelectorAll('select');
        for (var i = 0; i < selects.length; i++) selects[i].selectedIndex = 0;
        var inputs = clone.querySelectorAll('input');
        for (var i = 0; i < inputs.length; i++) inputs[i].value = '';
        produkList.appendChild(clone);
      };
      produkList.onclick = function (e) {
        e = e || window.event;
        var target = e.target || e.srcElement;
        if (target.classList.contains('remove-produk')) {
          e.preventDefault();
          var rows = produkList.querySelectorAll('.produk-row');
          if (rows.length > 1) {
            target.closest('.produk-row').remove();
          }
        }
      };
    };
  </script>
  <!-- Loading Overlay -->
  <div id="loading-overlay"
    style="display:none;position:fixed;z-index:9999;top:0;left:0;width:100vw;height:100vh;background:rgba(255,255,255,0.7);align-items:center;justify-content:center;">
    <div class="spinner-border text-primary" style="width:4rem;height:4rem;" role="status">
      <span class="visually-hidden">Loading...</span>
    </div>
  </div>
  <style>
    #loading-overlay.show {
      display: flex !important;
    }
  </style>
  <script>
    // Tampilkan loading saat pindah halaman
    document.addEventListener('DOMContentLoaded', function () {
      var overlay = document.getElementById('loading-overlay');
      // Untuk semua link kecuali anchor dengan # dan target _blank
      document.querySelectorAll('a').forEach(function (link) {
        link.addEventListener('click', function (e) {
          if (e.defaultPrevented) return;
          var href = link.getAttribute('href');
          if (
            href &&
            href !== '#' &&
            link.target !== '_blank' &&
            !link.hasAttribute('data-bs-toggle') &&
            // pengecualian untuk link download file
            !href.includes('/exportpdf') &&
            !href.includes('/exportexcel') &&
            !href.includes('/cetak_tgl_pdf') &&
            !href.includes('/export-terlaris-pdf') &&
            !href.includes('/export-stok-pdf')
          ) {
            overlay.classList.add('show');
          }
        });
      });
      document.querySelectorAll('form').forEach(function (form) {
        form.addEventListener('submit', function (e) {
          if (e.defaultPrevented) return;
          overlay.classList.add('show');
        });
      });
      window.addEventListener('pageshow', function () {
        overlay.classList.remove('show');
      });
    });
  </script>
  <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
  <script>
    AOS.init();
  </script>
  <script>
    // Sidebar Active State Correction Script
    // This overrides the fuzzy URL matching in the theme's misc.js
    $(document).ready(function() {
      // Give time for misc.js to finish its initialization
      setTimeout(function() {
        const currentPath = window.location.pathname;
        
        // Clear all active/show states first to be sure
        $('.sidebar .nav-item').removeClass('active');
        $('.sidebar .collapse').removeClass('show');
        $('.sidebar .nav-link').removeClass('active');

        // 1. Activate based on exact patterns
        $('.sidebar .nav-item').each(function() {
            const $item = $(this);
            if ($item.hasClass('active-ref')) {
                $item.addClass('active');
                if ($item.find('.collapse').length) {
                    $item.find('.collapse').addClass('show');
                }
            }
        });

        // 2. Activate sub-menu items
        $('.sidebar .nav-link').each(function() {
            const $link = $(this);
            const href = $link.attr('href');
            if (href && currentPath === href) {
                $link.addClass('active');
                $link.closest('.collapse').addClass('show');
                $link.parents('.nav-item').last().addClass('active');
            }
        });
      }, 100); 
    });
  </script>
  @yield('scripts')
</body>

</html>