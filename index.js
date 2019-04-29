function myfn() {
	//$('.likeform').submit(function () {
	var likelems = document.getElementsByClassName('likeform');

	for (var i = 0; i < likelems.length; i++) {
		likelems[i].onsubmit = function (formdata) {

			var request = new XMLHttpRequest();
			request.open('POST', '/like.php', true);
			request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
			request.onload = function () {
				if (request.status >= 200 && request.status < 400) {
					var resp = request.responseText;
					data = JSON.parse(resp);
					if (data['state'] == 1) {
						document.getElementById('likebtn' + data['pid']).style.backgroundColor = 'red';
						//$('#likebtn' + data['pid']).css('background-color', 'red');
					}
					else if (data['state'] == -1) {
						document.getElementById('likebtn' + data['pid']).style.backgroundColor = 'white';
						//$('#likebtn' + data['pid']).css('background-color', 'white');
					}
					document.getElementById('liketxt' + data['pid']).innerHTML = data['newval'] + " ♥";
					//$('#liketxt' + data['pid']).text(data['newval'] + " ♥");
				}
			};

			request.send(new URLSearchParams(new FormData(formdata['target'])).toString());

			/*
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
			*/
			return false;
		};
	}

	//$('.commform').submit(function () {
	var commelems = document.getElementsByClassName('commform');
	for (var i = 0; i < commelems.length; i++) {

		commelems[i].onsubmit = function (formdata) {

			var request = new XMLHttpRequest();
			request.open('POST', '/comment.php', true);
			request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
			request.onload = function () {
				if (request.status >= 200 && request.status < 400) {
					window.location.reload();
				}
			};
			request.send(new URLSearchParams(new FormData(formdata['target'])).toString());
			/*
			$.ajax({
				url: '/comment.php',
				type: 'post',
				data: $(this).serialize(),
				success: function (data) {
					console.log(data);
					window.location.reload();
				}
			});
			*/
			return false;
		};
	}
}

function ready(fn) {
	if (document.attachEvent ? document.readyState === "complete" : document.readyState !== "loading") {
		//$(document).ready(
		//);
		fn();
	} else {
		document.addEventListener('DOMContentLoaded', fn);
	}
}

ready(myfn);
