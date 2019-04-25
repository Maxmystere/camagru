$(document).ready(function () {
	$('.likeform').submit(function () {
		console.log($(this).serialize());
		$.ajax({
			url: '/like.php',
			type: 'post',
			data: $(this).serialize(),
			success: function (data) {
				data = JSON.parse(data);
				console.log(data);
				if (data['state'] == 1)
				{
					$('#likebtn' + data['pid']).css('background-color', 'red');
				}
				else if (data['state'] == -1)
				{
					$('#likebtn' + data['pid']).css('background-color', 'white');
				}
				$('#liketxt' + data['pid']).text(data['newval'] + " ♥");
			}
		});
		return false;
	});
	$('.commform').submit(function () {
		console.log($(this).serialize());
		$.ajax({
			url: '/comment.php',
			type: 'post',
			data: $(this).serialize(),
			success: function (data) {
				console.log(data);
				window.location.reload();
			}
		});
		return false;
	});
});
