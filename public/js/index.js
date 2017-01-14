jQuery(function($){
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});
	$(".reload").click(function(){
    var id = $("input[name='number-len']").val();
		window.location.replace("http://127.0.0.1:8000/"+id);
	});
	$(".user-interact input[type='submit']").click(function(){
		var guess = $("input[name='guess']").val();
		var game_id = $("input[name='game-id']").val();
		$.ajax({
			type: "POST",
  		url: "http://127.0.0.1:8000/",
			datatype:"json",
			data:{"guess":guess,"game-id":game_id}
		}).done(function(data){
			$(".guess-record div").append(data+"<br>");
		});
	});
	$(".download-btn").click(function(){
		var game_id = $("input[type='hidden']").val();
		window.location.replace("http://127.0.0.1:8000/download/"+game_id);
	});
});
