<!doctype html>
<html>
  <head>
    <meta charset="utf-8"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href={{ URL::asset("/css/app.css") }} />
  </head>
  <body class="well">
    <h1>猜數字</h1>
    <h2 class="hint">
      hint:
      <?php
        echo str_shuffle($guess);
        ?>
    </h2>
    <h3>選擇數字位數(預設四位, 最多十位)</h3>
    <div class="row">
      <div class="input-group col-lg-4 col-lg-offset-5 select-number">
        <input name="number-len" type="text" class="form-control" placeholder="4"/>
        <button class="reload btn btn-danger">重開新局</button>
      </div>
    </div>
    <div class="row">
      <div class="user-interact col-lg-4 col-lg-offset-4 input-group">
        <input name="guess" type="text" class="form-control" />
        <input name="game-id" type="hidden" value=<?php echo $game_id; ?>>
        <input name="submit" type="submit" class="btn btn-primary" value="我猜！"/>
      </div>
    </div>
    <div class="row">
      <div class="guess-record">
        <h3>您的作答記錄</h3> 
        <div class="col-lg-4 col-lg-offset-4">
        </div>
      </div>
    </div>
    <div class="row download">
      <button class="btn btn-default download-btn col-lg-2 col-lg-offset-5">下載作答記錄</button>
    </div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script src={{ URL::asset("/js/index.js") }}></script>
  </body>
<html>
