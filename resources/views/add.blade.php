@extends('layouts.app')

@section('content')

  <form class="form-horizontal" action="{{ route('url.add.new') }}" method="post">
    {{ csrf_field() }}

    <fieldset>

      <!-- Form Name -->
      <legend>Add Site</legend>

      <!-- Text input-->
      <div class="form-group">
        <label class="col-md-4 control-label" for="url">URL</label>
        <div class="col-md-6">
          <input id="url" name="url" type="text" placeholder="https://yourweb.location" class="form-control input-md" required="">
          <span class="help-block">Enter the full URL to monitor</span>
        </div>
      </div>

      <!-- Select Multiple -->
      <div class="form-group">
        <label class="col-md-4 control-label" for="features">Monitored Features</label>
        <div class="col-md-6">
          <select id="features" name="features[]" class="form-control" multiple="multiple">
            <option value="ssl" selected>Certificate (SSL)</option>
            <option value="headers" selected>Headers</option>
            <option value="cookies" selected>Cookies</option>
            <option value="body" selected>Body</option>
          </select>
        </div>
      </div>

      <!-- Button -->
      <div class="form-group">
        <label class="col-md-4 control-label" for="submit"></label>
        <div class="col-md-4">
          <button id="submit" name="submit" class="btn btn-default">Add</button>
        </div>
      </div>

    </fieldset>
  </form>

@stop

@section('javascript')

  <script>
    $('select#features').select2();
  </script>

@stop
