<nav class="navbar navbar-inverse navbar-fixed-top">
  <div class="container-fluid">
    <div class="navbar-header">
      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar"
              aria-expanded="false" aria-controls="navbar">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand" href="{{ route('dashboard') }}">
        {{ trans('httpmon.title') }}
      </a>
    </div>
    <div id="navbar" class="navbar-collapse collapse">
      <ul class="nav navbar-nav navbar-right">
        <li><a href="{{ route('url.new') }}">{{ trans('httpmon.add_site') }}</a></li>
      </ul>
      <form class="navbar-form navbar-right" action="{{ route('search') }}" method="post">
        {{ csrf_field() }}
        <input type="text" class="form-control" name="q" placeholder="{{ trans('httpmon.search') }}">
      </form>
    </div>
  </div>
</nav>