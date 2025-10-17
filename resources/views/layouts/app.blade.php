<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>One-Unborn</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.jpg') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- ✅ Flatpickr CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
   
    <!-- ✅ Flatpickr JS -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>


    <!-- ✅ Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">

    <!-- ✅ Select2 CSS (with Bootstrap 5 theme) -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />

    <!-- style -->
     <style>
    /* Sidebar default for large screens */
  #sidebar {
    width: 230px;
    min-height: 100vh;
    transition: all 0.3s;
    position: fixed;
    top: 0;
    left: 0;
    background-color: #121722ff;
    z-index: 100;
}


     /* ✅ Active & Hover Colors */
        .nav-link.menu-item {
            padding: 10px 15px;
            border-radius: 6px;
            transition: background-color 0.3s ease;
        }
        .nav-link.menu-item:hover {
            background-color: #1e40af;
            color: #fff !important;
        }
        .nav-link.menu-item.active {
            background-color: #0d6efd;
            color: #fff !important;
            font-weight: 600;
        }
        /* xx  */
        .menu-item.active {
    background-color: #0d6efd !important;
    border-radius: 6px;
}
#sidebar .collapse .nav-link {
    font-size: 0.95rem;
    padding-left: 1.8rem;
}

        /*  */

    /* Content shift */
    .content-wrapper {
    margin-left: 230px; /* a bit smaller for better balance */
    width: calc(100% - 230px);
    transition: margin-left 0.3s ease, width 0.3s ease;
    padding: 0; /* remove extra padding causing push */
}



    /* Sidebar collapsed (mobile) */
    @media (max-width: 768px) {
        #sidebar {
            position: fixed;
            left: -230px;
            top: 0;
            height: 100%;
            z-index: 999;
            transition: left 0.3s ease;
        }
        #sidebar.active {
            left: 0;
        }
        .content-wrapper {
            margin-left: 0 !important;
             width: 100% !important;
        /* transition: all 0.3s ease; */
        }
        /* ✅ Overlay for background dim */
    #sidebarOverlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 998;
    }

    #sidebar.active + #sidebarOverlay {
        display: block;
    }
        

        /*  */

        
    .form-control {
        border-radius: 8px;
        padding: 10px;
    }

    .form-group {
        margin-bottom: 15px;
    }


    }
</style>
@if(session('alert'))
  <div class="alert alert-warning text-center">{{ session('alert') }}</div>
@endif


</head>
<div id="sidebarOverlay"></div>

<body class="d-flex">
    @include('layouts.sidebar')
    <div class="flex-grow-1 content-wrapper">
        @include('layouts.navbar')
        <main class="p-4">
            @yield('content')
        </main>
    </div>
</body>
<!-- ✅ jQuery must come before Select2 and before your custom script -->
<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>

<!-- ✅ Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<!-- ✅ Bootstrap JS (after jQuery is fine) -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.min.js"></script>

<!-- ✅ Bootstrap Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">


<script>
$(document).ready(function() {
    // ✅ Initialize Select2 for multiple company selection
    $('#company_id').select2({
        theme: 'bootstrap-5',
        placeholder: "Select Companies",
        allowClear: true,
        width: '100%' // ensures full width styling
    });

    // ✅ Sidebar toggle
   // $('#sidebarToggle').on('click', function() {
     //   $('#sidebar').toggleClass('active');
   // });
    // ✅ Sidebar toggle for mobile
$('#sidebarToggle').on('click', function() {
    $('#sidebar').toggleClass('active');
    $('#sidebarOverlay').toggle();
});

// ✅ Close sidebar when overlay is clicked
$('#sidebarOverlay').on('click', function() {
    $('#sidebar').removeClass('active');
    $(this).hide();
});


    // ✅ Active menu item highlighting
    $('.menu-item').on('click', function() {
        $('.menu-item').removeClass('active');
        $(this).addClass('active');
    });

    flatpickr("input[type=date]", {
    dateFormat: "d-m-Y", // change format to DD-MM-YYYY
    altInput: true,      // shows friendly format
    altFormat: "F j, Y", // shows “October 6, 2025”
    allowInput: true,
    // theme: "dark"      // uncomment for dark mode
});

$(document).ready(function () {
    $('.select2-tags').select2({
        theme: 'bootstrap-5',
        tags: true, // ✅ allows typing new values
        placeholder: 'Select or Type',
        allowClear: true,
        width: '100%'
    });
});

});

</script>

</html>
