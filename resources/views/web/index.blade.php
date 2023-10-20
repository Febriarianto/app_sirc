<!doctype html>
<html lang="en">

<head>
    <title>ConceptAutorent</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <link rel="shortcut icon" href="{{ asset ('storage/images/setting/'.SettingWeb::get_setting()->favicon) }}" type="image/x-icon">

    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700;900&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset ('assets/web/fonts/icomoon/style.css') }}">
    <link rel="stylesheet" href="{{ asset ('assets/web/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset ('assets/web/css/bootstrap-datepicker.css') }}">
    <link rel="stylesheet" href="{{ asset ('assets/web/css/jquery.fancybox.min.css') }}">
    <link rel="stylesheet" href="{{ asset ('assets/web/css/owl.carousel.min.css') }}">
    <link rel="stylesheet" href="{{ asset ('assets/web/css/owl.theme.default.min.css') }}">
    <link rel="stylesheet" href="{{ asset ('assets/web/fonts/flaticon/font/flaticon.css') }}">
    <link rel="stylesheet" href="{{ asset ('assets/web/css/aos.css') }}">

    <!-- MAIN CSS -->
    <link rel="stylesheet" href="{{ asset ('assets/web/css/style.css') }}">

</head>

<body>


    <div class="site-wrap" id="home-section">

        <div class="site-mobile-menu site-navbar-target">
            <div class="site-mobile-menu-header">
                <div class="site-mobile-menu-close mt-3">
                    <span class="icon-close2 js-menu-toggle"></span>
                </div>
            </div>
            <div class="site-mobile-menu-body"></div>
        </div>



        <header class="site-navbar site-navbar-target" role="banner">

            <div class="container">
                <div class="row align-items-center position-relative">

                    <div class="col-3">
                        <div class="site-logo">
                            <a href="index.html"><strong>Concept </strong>AutoRent</a>
                        </div>
                    </div>

                    <div class="col-9  text-right">

                        <!-- <span class="d-inline-block d-lg-none"><a href="#" class=" site-menu-toggle js-menu-toggle py-5 "><span class="icon-menu h3 text-black"></span></a></span> -->

                        <!-- <nav class="site-navigation text-right ml-auto d-none d-lg-block" role="navigation">
                            <ul class="site-menu main-menu js-clone-nav ml-auto ">
                                <li><a href="index.html" class="nav-link">Home</a></li>
                                <li class="active"><a href="listing.html" class="nav-link">Listing</a></li>
                                <li><a href="testimonials.html" class="nav-link">Testimonials</a></li>
                                <li><a href="blog.html" class="nav-link">Blog</a></li>
                                <li><a href="about.html" class="nav-link">About</a></li>
                                <li><a href="contact.html" class="nav-link">Contact</a></li>
                            </ul>
                        </nav> -->
                    </div>


                </div>
            </div>

        </header>


        <div class="hero inner-page" style="background-image: url('assets/web/images/hero_1_a.jpg');">

            <div class="container">
                <div class="row align-items-end ">
                    <div class="col-lg-5">

                        <div class="intro">
                            <h1><strong>Daftar Mobil</strong></h1>
                            <div class="custom-breadcrumbs"><a href="">Daftar</a> <span class="mx-2">-</span>
                                <strong>Mobil</strong>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <div class="site-section bg-light">
            <div class="container">

                <div class="row">
                    <div class="col-lg-7">
                        <h2 class="section-heading"><strong>Daftar Mobil</strong></h2>
                        <p class="mb-5">Daftar Mobil Lepas Kunci</p>
                    </div>
                </div>
                <form class="trip-form" action="{{route('web.index')}}" method="post">
                    @csrf
                    <div class="row align-items-center">
                        <div class="mb-3 mb-md-0 col-md-6">
                            <select name="jenis" id="jenis" class="custom-select form-control">
                                <option value="">All</option>
                                @foreach($jenis as $j)
                                <option value="{{$j->id}}" {{ $j->id == $id_jenis ? 'selected' : ''}}>{{$j->nama}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3 mb-md-0 col-md-3">
                            <div class="form-control-wrap">
                                <input type="date" id="cf-3" name="tgl" class="form-control" value="{{ isset($tgl) ? $tgl : ''}}">
                            </div>
                        </div>
                        <div class="mb-3 mb-md-0 col-md-3">
                            <input type="submit" value="Search Now" class="btn btn-primary btn-block py-3">
                        </div>
                    </div>
                </form>
                <div class="row mt-5">
                    @foreach($kendaraan as $k)
                    <div class="col-md-6 col-lg-4 mb-4">

                        <div class="listing d-block  align-items-stretch">
                            <div class="listing-img h-100 mr-4">
                                <img src="{{ asset ('storage').'/'.$k->foto }}" alt="Image" width="300px" height="200px">
                            </div>
                            <div class="listing-contents h-100">
                                <h3>{{$k->nama}}</h3>
                                <div class="rent-price">
                                    <strong>Rp. {{ number_format($k->harga_12)}}</strong><span class="mx-1">/</span>12 Jam
                                </div>
                                <div class="rent-price">
                                    <strong>Rp. {{number_format($k->harga_24)}}</strong><span class="mx-1">/</span>24 Jam
                                </div>
                                <div class="d-block d-md-flex mb-3 border-bottom pb-3">
                                    <div class="listing-feature pr-4">
                                        <span class="caption">Warna:</span>
                                        <span class="number">{{$k->warna}}</span>
                                    </div>
                                    <div class="listing-feature pr-4">
                                        <span class="caption">Tahun:</span>
                                        <span class="number">{{$k->tahun}}</span>
                                    </div>
                                </div>
                                <div>
                                    <h3><span class="badge {{$k->id_kendaraan != null ? 'badge-danger' : 'badge-success'}} float-right">{{$k->id_kendaraan != null ? 'Tidak Tersedia' : 'Tersedia'}}</span></h3>
                                    <a href="{{$k->id_kendaraan != null ? '' : 'https://api.whatsapp.com/send/?phone=6281380101878&text=Hai%27Admin+Saya+ingin%27Menyewa+Mobil+'.$k->nama.'+warna%27'.$k->warna.'&type=phone_number&app_absent=0'}}" class="btn btn-success btn-sm {{$k->id_kendaraan != null ? 'disabled' : ''}}"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-whatsapp" viewBox="0 0 16 16">
                                            <path d="M13.601 2.326A7.854 7.854 0 0 0 7.994 0C3.627 0 .068 3.558.064 7.926c0 1.399.366 2.76 1.057 3.965L0 16l4.204-1.102a7.933 7.933 0 0 0 3.79.965h.004c4.368 0 7.926-3.558 7.93-7.93A7.898 7.898 0 0 0 13.6 2.326zM7.994 14.521a6.573 6.573 0 0 1-3.356-.92l-.24-.144-2.494.654.666-2.433-.156-.251a6.56 6.56 0 0 1-1.007-3.505c0-3.626 2.957-6.584 6.591-6.584a6.56 6.56 0 0 1 4.66 1.931 6.557 6.557 0 0 1 1.928 4.66c-.004 3.639-2.961 6.592-6.592 6.592zm3.615-4.934c-.197-.099-1.17-.578-1.353-.646-.182-.065-.315-.099-.445.099-.133.197-.513.646-.627.775-.114.133-.232.148-.43.05-.197-.1-.836-.308-1.592-.985-.59-.525-.985-1.175-1.103-1.372-.114-.198-.011-.304.088-.403.087-.088.197-.232.296-.346.1-.114.133-.198.198-.33.065-.134.034-.248-.015-.347-.05-.099-.445-1.076-.612-1.47-.16-.389-.323-.335-.445-.34-.114-.007-.247-.007-.38-.007a.729.729 0 0 0-.529.247c-.182.198-.691.677-.691 1.654 0 .977.71 1.916.81 2.049.098.133 1.394 2.132 3.383 2.992.47.205.84.326 1.129.418.475.152.904.129 1.246.08.38-.058 1.171-.48 1.338-.943.164-.464.164-.86.114-.943-.049-.084-.182-.133-.38-.232z" />
                                        </svg> Hubungi Admin</a>
                                </div>
                            </div>

                        </div>
                    </div>
                    @endforeach
                </div>
                {!! $kendaraan->links("pagination::bootstrap-4") !!}
            </div>
        </div>



        <footer class="site-footer">
            <div class="container">
                <div class="row">
                    <div class="col-lg-3">
                        <h2 class="footer-heading mb-4">About Us</h2>
                        <p>Far far away, behind the word mountains, far from the countries Vokalia and Consonantia, there live the
                            blind texts. </p>
                        <ul class="list-unstyled social">
                            <li><a href="#"><span class="icon-facebook"></span></a></li>
                            <li><a href="#"><span class="icon-instagram"></span></a></li>
                            <li><a href="#"><span class="icon-twitter"></span></a></li>
                            <li><a href="#"><span class="icon-linkedin"></span></a></li>
                        </ul>
                    </div>
                    <div class="col-lg-8 ml-auto">
                        <div class="row">
                            <!-- <div class="col-lg-3">
                                <h2 class="footer-heading mb-4">Quick Links</h2>
                                <ul class="list-unstyled">
                                    <li><a href="#">About Us</a></li>
                                    <li><a href="#">Testimonials</a></li>
                                    <li><a href="#">Terms of Service</a></li>
                                    <li><a href="#">Privacy</a></li>
                                    <li><a href="#">Contact Us</a></li>
                                </ul>
                            </div>
                            <div class="col-lg-3">
                                <h2 class="footer-heading mb-4">Resources</h2>
                                <ul class="list-unstyled">
                                    <li><a href="#">About Us</a></li>
                                    <li><a href="#">Testimonials</a></li>
                                    <li><a href="#">Terms of Service</a></li>
                                    <li><a href="#">Privacy</a></li>
                                    <li><a href="#">Contact Us</a></li>
                                </ul>
                            </div>
                            <div class="col-lg-3">
                                <h2 class="footer-heading mb-4">Support</h2>
                                <ul class="list-unstyled">
                                    <li><a href="#">About Us</a></li>
                                    <li><a href="#">Testimonials</a></li>
                                    <li><a href="#">Terms of Service</a></li>
                                    <li><a href="#">Privacy</a></li>
                                    <li><a href="#">Contact Us</a></li>
                                </ul>
                            </div>
                            <div class="col-lg-3">
                                <h2 class="footer-heading mb-4">Company</h2>
                                <ul class="list-unstyled">
                                    <li><a href="#">About Us</a></li>
                                    <li><a href="#">Testimonials</a></li>
                                    <li><a href="#">Terms of Service</a></li>
                                    <li><a href="#">Privacy</a></li>
                                    <li><a href="#">Contact Us</a></li>
                                </ul>
                            </div> -->
                        </div>
                    </div>
                </div>
                <div class="row pt-5 mt-5 text-center">
                    <div class="col-md-12">
                        <div class="border-top pt-5">
                            <p>
                                <!-- Link back to Colorlib can't be removed. Template is licensed under CC BY 3.0. -->
                                Copyright &copy;
                                <script>
                                    document.write(new Date().getFullYear());
                                </script> All rights reserved | This template is made
                                with <i class="icon-heart text-danger" aria-hidden="true"></i> by <a href="" target="_blank"></a>
                                <!-- Link back to Colorlib can't be removed. Template is licensed under CC BY 3.0. -->
                            </p>
                        </div>
                    </div>

                </div>
            </div>
        </footer>

    </div>
    <script src="{{ asset ('assets/web/js/jquery-3.3.1.min.js') }}"></script>
    <script src="{{ asset ('assets/web/js/popper.min.js') }}"></script>
    <script src="{{ asset ('assets/web/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset ('assets/web/js/owl.carousel.min.js') }}"></script>
    <script src="{{ asset ('assets/web/js/jquery.sticky.js') }}"></script>
    <script src="{{ asset ('assets/web/js/jquery.waypoints.min.js') }}"></script>
    <script src="{{ asset ('assets/web/js/jquery.animateNumber.min.js') }}"></script>
    <script src="{{ asset ('assets/web/js/jquery.fancybox.min.js') }}"></script>
    <script src="{{ asset ('assets/web/js/jquery.easing.1.3.js') }}"></script>
    <script src="{{ asset ('assets/web/js/bootstrap-datepicker.min.js') }}"></script>
    <script src="{{ asset ('assets/web/js/aos.js') }}"></script>
    <script src="{{ asset ('assets/web/js/main.js') }}"></script>

    <script>
        $(function() {
            var dtToday = new Date();
            var month = dtToday.getMonth() + 1;
            var day = dtToday.getDate();
            var year = dtToday.getFullYear();
            if (month < 10)
                month = '0' + month.toString();
            if (day < 10)
                day = '0' + day.toString();
            var maxDate = year + '-' + month + '-' + day;
            $('#cf-3').attr('min', maxDate);
        });
    </script>

</body>

</html>