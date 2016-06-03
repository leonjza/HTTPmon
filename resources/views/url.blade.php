@extends('layouts.app')

@section('content')

  <div class="panel panel-default">
    <div class="panel-heading">
      {{ $url->url }}
      <span class="pull-right" data-toggle="tooltip" data-placement="top"
            title="{{ $url->last_check }}">
                @if($url->last_check)
          Updated {{ $url->last_check->diffForHumans() }}
          <a href="{{ route('url.update', ['id' => $url->id]) }}"
             class="btn btn-xs btn-primary">
            Update Now
          </a>
        @endif
      </span>
    </div>
    <div class="panel-body">

      <div class="row placeholders">
        <div class="col-xs-6 col-sm-3 placeholder">
          <h1>{{ $url->meta->status_code }}</h1>
          <h4>HTTP</h4>
          <span class="text-muted">Status Code</span>
        </div>
        <div class="col-xs-6 col-sm-3 placeholder">
          <h1>{{ $url->meta->reason_phrase }}</h1>
          <h4>HTTP</h4>
          <span class="text-muted">Reason Phrase</span>
        </div>
        <div class="col-xs-6 col-sm-3 placeholder">
          <h1>{{ $url->meta->protocol_version }}</h1>
          <h4>HTTP</h4>
          <span class="text-muted">Protocol Version</span>
        </div>
        <div class="col-xs-6 col-sm-3 placeholder">
          <h1>{{ $url->meta->server }}</h1>
          <h4>HTTP</h4>
          <span class="text-muted">Server Banner</span>
        </div>
      </div>

      <div class="row placeholders">
        <div class="col-xs-6 col-sm-3 placeholder">
          <h1>{{ count($url->cookies) }}</h1>
          <h4>Total</h4>
          <span class="text-muted">Cookies Set</span>
        </div>
        <div class="col-xs-6 col-sm-3 placeholder">
          <h1>{{ count($url->headers) }}</h1>
          <h4>Total</h4>
          <span class="text-muted">Response Headers</span>
        </div>
        <div class="col-xs-6 col-sm-3 placeholder">
          <h1>
            @if(!is_null($url->ssl))
              <span data-toggle="tooltip" data-placement="top"
                    title="{{ $url->ssl->valid_to }}">
                {{-- can happen that we only have a grade --}}
                @if(!empty($url->ssl->valid_to))
                  {{ $url->ssl->valid_to->diffForHumans() }}
                @endif
              </span>
            @else
              n/a
            @endif
          </h1>
          <h4>SSL</h4>
          <span class="text-muted">Certificate Expiry</span>
        </div>
        <div class="col-xs-6 col-sm-3 placeholder">
          <h1>
            <span data-toggle="tooltip" data-placement="top"
                  title="{{ $url->body->sha256 }}">
                {{ str_limit($url->body->sha256, 8, '') }}
            </span>
          </h1>
          <h4>Response Body</h4>
          <span class="text-muted">Shortened SHA256 Sum</span>
        </div>
      </div>

    </div>

    <div class="panel-body">

      @if(!empty($url->ssl))

        <h2 class="sub-header">
          SSL Certificate Information
          <span class="pull-right">
            SSL Rating: {{ $url->ssl->ssl_labs_rating }}
          </span>
        </h2>

        <div class="row placeholders">
          <div class="col-xs-6 col-sm-4 placeholder">

            <dl>
              <dt>Common Name</dt>
              <dd>{{ $url->ssl->cn }}</dd>
            </dl>

            <dl>
              <dt>Issuer</dt>
              <dd>
                {{
                  implode(', ', collect($url->ssl->issuer)->map(function($key, $value) {

                    if (is_array($key))
                      $key = implode(',', $key);

                    return $key . '=' . $value;
                  })->toArray())
                }}
              </dd>
            </dl>

          </div>
          <div class="col-xs-6 col-sm-4 placeholder">

            <dl>
              <dt>signatureTypeLN</dt>
              <dd>{{ $url->ssl->signature_type_ln }}</dd>
            </dl>

            <dl>
              <dt>Subject</dt>
              <dd>
                {{
                  implode(', ', collect($url->ssl->subject)->map(function($key, $value) {

                      if (is_array($key))
                        $key = implode(',', $key);

                    return $key . '=' . $value;
                  })->toArray())
                }}
              </dd>
            </dl>

          </div>
          <div class="col-xs-6 col-sm-4 placeholder">

            <dl>
              <dt>Key Bits</dt>
              <dd>{{ $url->ssl->key_bits }}</dd>
            </dl>

            <dl>
              <dt>Serial Number</dt>
              <dd>
                {{ $url->ssl->serial_number }}
              </dd>
            </dl>

          </div>
        </div>

        <div class="row placeholders">
          <div class="col-xs-6 col-sm-12 placeholder">

            <dl>
              <dt>Public Key</dt>
              <dd>
                <pre>{{ $url->ssl->public_key }}</pre>
              </dd>
            </dl>

          </div>
        </div>

      @endif

      <h2 class="sub-header">Headers</h2>

      <table class="table table-striped table-hover table-condensed">
        <thead>
        <tr>
          <th>Name</th>
          <th>Value</th>
        </tr>
        </thead>
        <tbody>

        @foreach($url->headers as $header)
          <tr>
            <th>{{ $header->name }}</th>
            <td>
              <ul>
                @foreach($header->values as $value)
                  <li>{{ str_limit($value, 100) }}</li>
                @endforeach
              </ul>
            </td>
          </tr>
        @endforeach

        </tbody>
      </table>

      <h2 class="sub-header">Cookies</h2>

      <table class="table table-striped table-hover table-condensed">
        <thead>
        <tr>
          <th>Name</th>
          <th>Value</th>
        </tr>
        </thead>
        <tbody>

        @foreach($url->cookies as $cookie)
          <tr>
            <th>{{ $cookie->name }}</th>
            <td>{{ str_limit($cookie->value, 100) }}</td>
          </tr>
        @endforeach

        </tbody>
      </table>
    </div>

  </div>

  <div class="panel panel-default">
    <div class="panel-body">

      <form class="form" action="{{ route('url.update.features') }}" method="post">
        <fieldset>
          {{ csrf_field() }}

          <input name="id" type="hidden" value="{{ $url->id }}">

          <!-- Multiple Checkboxes (inline) -->
          <div class="form-group">
            <label class="col-md-4 control-label" for="features">Monitored Features</label>
            <div class="col-md-6">
              <select id="features" name="features[]" class="form-control" multiple="multiple">
                <option value="ssl"
                        @if(in_array('ssl', $url->features)) selected @endif>Certificate (SSL)
                </option>
                <option value="headers"
                        @if(in_array('headers', $url->features)) selected @endif>Headers
                </option>
                <option value="cookies"
                        @if(in_array('cookies', $url->features)) selected @endif>Cookies
                </option>
                <option value="body"
                        @if(in_array('body', $url->features)) selected @endif>Body
                </option>
              </select>
            </div>

            <div class="pull-right">
              <button class="btn btn-default">
                Update
              </button>
            </div>

          </div>

        </fieldset>
      </form>

    </div>
  </div>

@stop

@section('javascript')

  <script>
    $('select#features').select2();
  </script>

@stop
