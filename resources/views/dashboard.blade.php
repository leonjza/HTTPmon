@extends('layouts.app')

@section('content')

  <h1 class="page-header">
    Dashboard
    <span class="text-muted">
      @if(!empty($term)) | Search Term: {{ $term }} @endif
    </span>
  </h1>

  <div class="row placeholders">
    <div class="col-xs-6 col-sm-4 placeholder">
      <h1>{{ count($urls) }}</h1>
      <h4>URLs</h4>
      <span class="text-muted">Monitored</span>
    </div>
    <div class="col-xs-6 col-sm-4 placeholder">
      <h1>
        {{
          $urls->filter(function($url) {

            if(!empty($url->ssl))
              if($url->ssl->valid_to < \Carbon\Carbon::now()->addMonth(3))
                return true;

            return false;

          })->count()
        }}
      </h1>
      <h4>Certificates</h4>
      <span class="text-muted">Expiring within 3 Months</span>
    </div>
    <div class="col-xs-6 col-sm-4 placeholder">
      <h1>
        {{
          \Carbon\Carbon::createFromTimestamp($urls->map(function($url) {

            if(!empty($url->ssl))
              if(!empty($url->ssl->valid_to))
                return $url->ssl->valid_to->timestamp;

            // Just return now if there is no timestamp
            return \Carbon\Carbon::now()->timestamp;

          })->avg())->diffForHumans()
        }}
      </h1>
      <h4>Average</h4>
      <span class="text-muted">SSL Expiry Time</span>
    </div>
  </div>

  <h2 class="sub-header">Monitored Urls</h2>

  <div class="table-responsive">
    <table class="table table-striped table-hover table-condensed">
      <thead>
      <tr>
        <th>#</th>
        <th>Last Check</th>
        <th>URL</th>
        <th>HTTP Status</th>
        <th>SSL Expiry</th>
        <th>SSL Rating</th>
        <th></th>
      </tr>
      </thead>
      <tbody>

      @foreach($urls as $url)
        <tr>
          <td>{{ $url->id }}</td>
          <td>
            <span data-toggle="tooltip" data-placement="top"
                  title="{{ $url->last_check }}">
              @if($url->last_check)
                {{ $url->last_check->diffForHumans() }}
              @endif
            </span>
          </td>
          <td>
            <a href="{{ $url->url }}" target="_blank" rel="noopener noreferrer">
              {{ $url->url }}
            </a>
          </td>
          <td>
            @if(!empty($url->meta))
              {{ $url->meta->status_code }}
            @endif
          </td>
          <td>
            @if(!empty($url->ssl))
              <span data-toggle="tooltip" data-placement="top"
                    title="{{ $url->ssl->valid_to }}">
                {{-- can happen that we only have a grade --}}
                @if(!empty($url->ssl->valid_to))
                  {{ $url->ssl->valid_to->diffForHumans() }}
                @endif
              </span>
            @endif
          </td>
          <td>
            @if(!empty($url->ssl))
              <span data-toggle="tooltip" data-placement="top"
                    title="Last update: {{ $url->ssl->ssl_labs_last_update }}">
                <a href="https://www.ssllabs.com/ssltest/analyze.html?viaform=on&d={{ $url->url }}&hideResults=on"
                   target="_blank" rel="noopener noreferrer">
                  {{ $url->ssl->ssl_labs_rating }}
                </a>
                </span>
            @endif
          </td>
          <td>
            <a href="{{ route('url', ['id' => $url->id]) }}">
              <span class="glyphicon glyphicon-zoom-in" aria-hidden="true"></span>
            </a>
            <a href="{{ route('url.delete', ['id' => $url->id]) }}" class="confirm">
              <span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
            </a>
          </td>
        </tr>
      @endforeach

      </tbody>
    </table>
  </div>

@stop