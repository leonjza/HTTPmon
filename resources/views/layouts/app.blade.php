<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <meta name="description" content="HTTPmon">
  <meta name="author" content="@leonjza">
  <link rel="icon" href="favicon.ico">

  <title>{{ trans('httpmon.title') }} v{{ config('httpmon.version') }}</title>

  <!-- Latest compiled and minified CSS -->
  <link rel="stylesheet" href="{{ asset('assets/bootstrap.min.css') }}"
        integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7"
        crossorigin="anonymous">

  <!-- Bootstrap theme -->
  <link rel="stylesheet" href="{{ asset('assets/bootstrap-theme.min.css') }}"
        integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r"
        crossorigin="anonymous">

  <!-- Custom styles for this template -->
  <link rel="stylesheet" href="{{ asset('assets/dashboard.css') }}"
        integrity="sha384-XolXXywmWr/wQrMGzbf5IEPGGXP6wjjwvXCdtm/rQLlKIYXi/j1iByiQZQNkr+lO"
        crossorigin="anonymous">

  <!-- Select2 Styles -->
  <link rel="stylesheet" href="{{ asset('assets/select2.min.css') }}"
        integrity="sha384-HIipfSYbpCkh5/1V87AWAeR5SUrNiewznrUrtNz1ux4uneLhsAKzv/0FnMbj3m6g"
        crossorigin="anonymous">

  <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->
</head>

<body>

@include('components.header')

<div class="container-fluid">

  <div class="row">

    @include('components.sidebar')

    <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">

      @include('components.notifications')

      @yield('content')

    </div>
  </div>
</div>

<!-- Bootstrap core JavaScript
================================================== -->
<!-- Jquery -->
<script src="{{ asset('assets/jquery.min.js') }}"
        integrity="sha384-6ePHh72Rl3hKio4HiJ841psfsRJveeS+aLoaEf3BWfS+gTF0XdAqku2ka8VddikM"
        crossorigin="anonymous"></script>

<!-- Latest compiled and minified JavaScript -->
<script src="{{ asset('assets/bootstrap.min.js') }}"
        integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS"
        crossorigin="anonymous"></script>

<!-- Select 2 -->
<script src="{{ asset('assets/select2.min.js') }}"
        integrity="sha384-222hzbb8Z8ZKe6pzP18nTSltQM3PdcAwxWKzGOKOIF+Y3bROr5n9zdQ8yTRHgQkQ"
        crossorigin="anonymous"></script>

<!-- Bootbox -->
<script src="{{ asset('assets/bootbox.min.js') }}"
        integrity="sha384-Nk2l95f1t/58dCc4FTWQZoXfrOoI2DkcpUvgbLk26lL64Yx3DeBbeftGruSisV3a"
        crossorigin="anonymous"></script>

<!-- Other -->
<script>

  // Init Tooltips
  $(function () {
    $('[data-toggle="tooltip"]').tooltip()
  })

  // Init bootbox for confirmations
  $('.confirm').click(function (e) {

    e.preventDefault();
    var $link = $(this);

    bootbox.confirm('Are you sure?', function (confirmation) {

      confirmation && document.location.assign($link.attr('href'));
    });

  })
</script>

@yield('javascript')

</body>
</html>
