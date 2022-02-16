<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
    <link rel="icon" href="{{asset('assets/landing')}}/image/logo_no_bg.png">

    <!-- Bootstrap+JQuery -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.js"
            integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.min.js"></script>


    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/owl-carousel/1.3.3/owl.carousel.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/owl-carousel/1.3.3/owl.theme.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/owl-carousel/1.3.3/owl.transitions.css">

    <!-- fontawesome -->
    <link rel="stylesheet" href="fontawesome/css/all.min.css">
    <link rel="stylesheet" href="fontawesome/css/fontawesome.min.css">

    <!-- Normalize CSS -->
    <link rel="stylesheet" href="{{asset('assets/landing')}}/css/normalize.css">

    <!-- font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@500;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700;900&display=swap" rel="stylesheet">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{asset('assets/landing')}}/css/main.css">
    <style>
        a:hover {
            color: #000000!important;
            text-decoration: underline;
        }
    </style>
</head>

<body>
<header>
    <div class="navbar-div bg-color-primary">
        <nav class="navbar navbar-expand-md">
            <div class="container">
                <a class="navbar-brand" href="{{route('home')}}">
                    <img  onerror="this.src='{{asset('uploads\users\default.jpg')}}'"
                          src="{{asset('uploads\users\default.jpg')}}"
                          style="height:auto;width:100%; max-width:200px; max-height:60px">
                </a>
                <button style="background: #FFFFFF; border-radius: 2px;font-size: 13px" class="navbar-toggler" type="button"
                        data-toggle="collapse" data-target="#navbarNav">
                   ....
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav mr-auto"></ul>
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link navbar-font" href="{{url('/')}}">home<span
                                    class="sr-only">(current)</span></a>
                        </li>
{{--                        <li class="nav-item">--}}
{{--                            <a class="nav-link navbar-font" href="#">terms and condition</a>--}}
{{--                        </li>--}}
{{--                        <li class="nav-item">--}}
{{--                            <a class="nav-link navbar-font" href="#">about us</a>--}}
{{--                        </li>--}}
{{--                        <li class="nav-item">--}}
{{--                            <a class="nav-link navbar-font" href="#">contact us</a>--}}
{{--                        </li>--}}
                        <li class="nav-item">
                            <a class="nav-link navbar-font" href="{{url('/privacy_policy')}}">privacy policy</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </div>
</header>

@yield('content')

<footer>
    <div class="footer-div">
        <!-- Footer Start -->
        <footer class="footer-background text-white text-lg-start">
            <!-- Grid container -->
            <div class="container">
                <!--Grid row-->
                <div class="row d-flex justify-content-center justify-content-md-start text-center text-md-left">
                    <!--Grid column-->
                    <div class="col-lg-3 col-md-3 mb-md-0 company_details">
                        <div
                            class="row d-flex justify-content-center justify-content-md-start text-center text-md-left">
                            <div class="col-md-12 col-sm-12 d-flex justify-content-center justify-content-md-start text-center text-md-left"
                                 style="padding: 0">
                                <a class="" href="#">
                                    <img class="rounded float-left"
                                         onerror="this.src='{{asset('uploads\users\default.jpg')}}'"
                                         src="{{asset('uploads\users\default.jpg')}}"
                                         style="max-width: 200px;max-height: 75px">
                                </a>
                            </div>
                        </div>

                        <div class="footer-article-div">
                                <span class="footer-article">
                                Aljoud
                                </span>
                        </div>

                        <div class="mt-4">
                            <a href="#" class="text-white"><i class="fab fa-twitter"></i></a>
                            <a href="#" class="text-white" style="margin-left: 44px"><i
                                    class="fab fa-facebook-f"></i></a>
                            <a href="#" class="text-white" style="margin-left: 44px"><i
                                    class="fab fa-linkedin"></i></a>
                            <a href="#" class="text-white" style="margin-left: 44px"><i
                                    class="fab fa-skype"></i></a>
                        </div>
                    </div>

                    <hr class="hr-footer-m">

                    <div class="col-lg-2 col-md-2 mb-0 mb-md-0"></div>
                    <!--Grid column-->
                    <div class="col-lg-2 col-md-2 mb-md-0 footer-items">
{{--                        <span class="footer-title text-uppercase mb-4">{{__('messages.support')}}</span>--}}

{{--                        <ul class="list-unstyled">--}}
{{--                            <li>--}}
{{--                                <a href="{#" class="footer-item text-white">{{__('messages.about_us')}}</a>--}}
{{--                            </li>--}}
{{--                            <li>--}}
{{--                                <a href="#" class="footer-item text-white">{{__('messages.contact_us')}}</a>--}}
{{--                            </li>--}}
{{--                            <li>--}}
{{--                                <a href="#" class="footer-item text-white">{{__('messages.privacy_policy')}}</a>--}}
{{--                            </li>--}}
{{--                            <li>--}}
{{--                                <a href="{{url('terms')}}" class="footer-item text-white">{{__('messages.terms_and_condition')}}</a>--}}
{{--                            </li>--}}
{{--                        </ul>--}}
                    </div>

                    <hr class="hr-footer-m">

                    <!--Grid column-->
                    <div class="col-lg-2 col-md-2 mb-md-0 footer-items">
{{--                        <span class="footer-title text-uppercase mb-4">Download</span>--}}

{{--                        <ul class="list-unstyled">--}}
{{--                            <li>--}}
{{--                                <a href="{{$landing_page_links['app_url_android']}}" class="footer-item text-white">Play Store</a>--}}
{{--                            </li>--}}
{{--                            <li class="mb-2">--}}
{{--                                <a href="{{$landing_page_links['app_url_ios']}}" class="footer-item text-white">App Store</a>--}}
{{--                            </li>--}}
{{--                        </ul>--}}
                    </div>

                    <hr class="hr-footer-m">

                    <!--Grid column-->
                    <div class="col-lg-3 col-md-3 mb-md-0 footer-items">
                        <span class="footer-title text-uppercase mb-4">Contact Us</span>

                        <ul class="list-unstyled mb-0">

                            <li class="mb-2">
                                <a href="mailto:aljoud@gmail.com" class="footer-item text-white">
                                    <i class="fas fa-envelope MR-1"></i>
                                    <span class="ml-1">aljoud.edu12@gmail.com</span>
                                </a>
                            </li>
                            <li class="mb-2">
                                <a href="tel:+966 53 258 5840" class="footer-item text-white">
                                    <i class="fas fa-phone MR-1"></i>
                                    <span class="ml-1">+966 53 258 5840</span>
                                </a>
                            </li>
                        </ul>
                    </div>

                    <hr class="hr-footer-m">
                </div>
            </div>

            <!-- Copyright -->
            <div class="text-center" style="background-color: rgba(0, 0, 0, 0.2);font-size: 12px">


            </div>
        </footer>
    </div>
</footer>


<!-- Scrips Starts -->
<script type="text/javascript" src="https://code.jquery.com/jquery-1.12.0.min.js"></script>
<script type="text/javascript"
        src="https://cdnjs.cloudflare.com/ajax/libs/owl-carousel/1.3.3/owl.carousel.min.js"></script>

<script>
    $(document).ready(function () {
        $("#testimonial-slider").owlCarousel({
            items: 2,
            itemsDesktop: [1000, 2],
            itemsDesktopSmall: [979, 2],
            itemsTablet: [767, 1],
            pagination: true,
            autoPlay: true
        });

        $("#why-choose-us-slider").owlCarousel({
            items: 3,
            itemsDesktop: [1000, 2],
            itemsDesktopSmall: [979, 2],
            itemsTablet: [767, 1],
            pagination: true,
            autoPlay: true
        });
    });
</script>

</body>

</html>
