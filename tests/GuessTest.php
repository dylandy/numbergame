<?php

use Illuminate\Support\Facades\Redis;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class GuessControllerTest extends TestCase
{
    /**
     *
     * @return void
     */
    public function testGameGenerationForID()
    {
        $current_game_id = max([] == Redis::smembers("game_id") ? [0] : Redis::smembers("game_id"));
        $this -> visit("/");
        assert(max(Redis::smembers("game_id")) == ($current_game_id + 1)); 
    }
    public function testGameGenerationForUniqueRandomNumberAndCorrectLength()
    {
      for ($len = 1; $len <= 10 ; $len ++) {
          $this -> visit("/" . $len);
          $current_game_id = max(Redis::smembers("game_id"));
          $latest_generated_random_number = Redis::get($current_game_id . "_guess");
          $check = [];
          $flag = true;
          if ($len == strlen($latest_generated_random_number)) {
              for ($i = 0 ; $i < strlen($latest_generated_random_number); $i++) {
                  if (in_array($latest_generated_random_number[$i], $check)) {
                      $flag = false;
                  } else {
                      array_push($check, $latest_generated_random_number[$i]);
                  }
              }
          } else {
              $flag = false;
          }
          assert($flag == true);
      }
    }
    public function testCheckABCorrectnessForDuplicatedNumbers()
    {
      $this -> visit("/");
      $current_game_id = max(Redis::smembers("game_id"));
      $test_data = "1234";
      $duplicated_guess_data = "2234";
      Redis::set($current_game_id . "_guess", $test_data);
      $this -> json("POST", "/", ["game-id" => $current_game_id , "guess" => $duplicated_guess_data ]) -> see("請輸入4個不重複的數字");

    }
    public function testCheckABCorrectnessForOverflowNumbers()
    {
      $this -> visit("/");
      $current_game_id = max(Redis::smembers("game_id"));
      $test_data = "1234";
      $overflow_guess_data = "12345";
      Redis::set($current_game_id . "_guess", $test_data);
      $this -> json("POST", "/", ["game-id" => $current_game_id , "guess" => $overflow_guess_data ]) -> see("請輸入4個不重複的數字");
    }
    public function testCheckABCorrectnessWhenGuessUnCorrect()
    {
      $this -> visit("/");
      $current_game_id = max(Redis::smembers("game_id"));
      $test_data = "1234";
      $guess_data = "2345";
      Redis::set($current_game_id . "_guess", $test_data);
      $this -> json("POST", "/", ["game-id" => $current_game_id , "guess" => $guess_data ]) -> see("2345: 0A3B");
    }
    public function testCheckABCorrectnessWhenGuessISCorrect()
    {
      $this -> visit("/");
      $current_game_id = max(Redis::smembers("game_id"));
      $test_data = "1234";
      $guess_data = "1234";
      Redis::set($current_game_id . "_guess", $test_data);
      $this -> json("POST", "/", ["game-id" => $current_game_id , "guess" => $guess_data ]) -> see("1234:正解");
    }
    public function testDownloadFunctioningCorrectly()
    {
      $this -> visit("/");
      $current_game_id = max(Redis::smembers("game_id"));
      $this -> visit("/download" . $current_game_id) -> assertResponseOk();
    }
}
