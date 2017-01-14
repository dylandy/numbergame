<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Redis;
use Illuminate\Http\Request;

class GuessController extends Controller
{

    /*
     * create a new game and store to redis
     * return void
     */

    private function gameInitialize($len)
    {
        // create new game by checking last game id in redis storage
        $last_game_id = max([] == Redis::smembers("game_id") ? [0] : Redis::smembers("game_id"));
        $new_game_id = $last_game_id + 1;
        $numbers = range(0, 9);
        shuffle($numbers);
        $numbers = implode("", array_slice($numbers, 0, $len));
        Redis::sadd("game_id", $new_game_id);
        Redis::set($new_game_id . "_guess", $numbers);
    }

    public function index($id = 4)
    {
        $this -> gameInitialize($id);
        $game_id = max(Redis::smembers("game_id"));
        $guess = Redis::get($game_id . "_guess");
        return view('guess.index', compact(['guess', 'game_id']));
    }

    public function inputCheck(Request $params)
    {
        $guess = $params -> input('guess');
        $check_same = [];
        $id = $params -> input('game-id');
        $answer = Redis::get($id . "_guess");
        $guess_time = ([] == Redis::keys("*_guessed_" . $id) ? 0  : explode("_", max(Redis::keys("*_guessed_" . $id)))[0]);

        // check if there are something the same in the guess string
        for ($i = 0; $i < strlen($guess); $i++) {
            if (in_array($guess[$i], $check_same)) {
                Redis::set($guess_time+1 . "_guessed_" . $id, "請輸入" . strlen($answer) . "個不重複的數字\n");
                return("請輸入" . strlen($answer) . "個不重複的數字");
            } else {
                array_push($check_same, $guess[$i]);
            }
        }

        $a_ans = 0;
        $b_ans = 0;

        if (strlen($answer) == strlen($guess)) {
            for ($i = 0; $i < strlen($guess); $i++) {
                if ($answer[$i] == $guess[$i]) {
                    $a_ans += 1;
                } elseif ("integer" == gettype(strpos($answer, $guess[$i])) && $i != strpos($answer, $guess[$i])) {
                    $b_ans += 1;
                }
            }
            if ($a_ans == 4) {
                Redis::set($guess_time+1 . "_guessed_" . $id, $guess.":正解\n");
                return($guess.":正解");
            } else {
                Redis::set($guess_time+1 . "_guessed_" . $id, $guess . ": ". $a_ans . "A" . $b_ans . "B\n");
                return($guess . ": ".$a_ans . "A" . $b_ans . "B");
            }
        } else {
            Redis::set($guess_time+1 . "_guessed_" . $id, "請輸入" . strlen($answer) . "個不重複的數字\n");
            return("請輸入" . strlen($answer) . "個不重複的數字");
        }
    }

    public function downloadRecord($id)
    {
        $record_keys = Redis::keys("*_guessed_" . $id);
        $res_text = "";
        $res_name = "solving.txt";
        for ($i = 1; $i <= count($record_keys); $i++) {
            $res_text .= Redis::get($i . "_guessed_" . $id);
        }
        $headers = ['Content-type'=>'text/plain', 'test'=>'YoYo', 'Content-Disposition'=>sprintf('attachment; filename="%s"', $res_name),'X-BooYAH'=>'WorkyWorky','Content-Length'=>strlen($res_text)];
        return \Response::make($res_text, 200, $headers);
    }
}
