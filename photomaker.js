var canvas;// = document.getElementById('videoCanvas');
var canvassnap;// = document.getElementById('videoSnapshot');
var context;// = canvas.getContext('2d');
var captureButton;// = document.getElementById('capture');
var dataURL;

function stop(e) {
	var video = document.querySelector("#videoElement");

	var stream = video.srcObject;
	var tracks = stream.getTracks();

	for (var i = 0; i < tracks.length; i++) {
		var track = tracks[i];
		track.stop();
	}

	video.srcObject = null;
}

var camstatus = 0;

function imgclick(id) {
	if (typeof imgclick.last == 'undefined')
		imgclick.last = 0;
	else
		document.getElementById('meme' + imgclick.last).style.backgroundColor = "#207020";
	document.getElementById('meme' + id).style.backgroundColor = "#209020";
	if (camstatus)
		document.getElementById("capture").disabled = false;
	//	console.log("last " + imgclick.last);
	//	console.log(id);
	imgclick.last = id;
	drawCanvas();
}

function drawCanvas() {
	context.clearRect(0, 0, canvas.width, canvas.height);

	if (document.getElementById("fileInput").style.display == "none")
	{
		context.drawImage(video, 0, 0);
		if (typeof imgclick.last != 'undefined') {
			context.drawImage(document.getElementById('meme' + imgclick.last), 0, 0, canvas.width, canvas.height);
		}
	}
	else
	{
		var image = new Image();

		image.onload = function () {
			context.drawImage(image, 0, 0, canvas.width, canvas.height);
			if (typeof imgclick.last != 'undefined') {
				context.drawImage(document.getElementById('meme' + imgclick.last), 0, 0, canvas.width, canvas.height);
			}
		};
		image.src = dataURL;
	}
}

window.onload = function () {

	canvas = document.getElementById('videoCanvas');
	canvassnap = document.getElementById('videoSnapshot');
	context = canvas.getContext('2d');
	captureButton = document.getElementById('capture');

	var video = document.querySelector("#videoElement");

	video.addEventListener('loadedmetadata', function () {
		camstatus = 1;
		canvas.width = video.videoWidth;
		canvas.height = video.videoHeight;
		if (typeof imgclick.last != 'undefined')
			document.getElementById("capture").disabled = false;
	});

	if (navigator.mediaDevices.getUserMedia) {
		navigator.mediaDevices.getUserMedia({ video: true })
			.then(function (stream) {
				video.srcObject = stream;
			})
			.catch(function (err0r) {
				console.log("Something went wrong! " + err0r);
				document.getElementById("fileInput").style.display = "inline-block";
			});
	}

	video.addEventListener('play', function () {
		var $this = this; //cache
		(function loop() {
			if (!$this.paused && !$this.ended) {
				context.drawImage($this, 0, 0);
				if (typeof imgclick.last != 'undefined') {
					context.drawImage(document.getElementById('meme' + imgclick.last), 0, 0, canvas.width, canvas.height);
				}
				setTimeout(loop, 1000 / 30); // drawing at 30fps
			}
		})();
	}, 0);

	var fileInput = document.getElementById('fileInput');

	fileInput.addEventListener('change', function (e) {
		var file = document.querySelector('input[type=file]').files[0];
		var reader = new FileReader();

		reader.addEventListener("load", function () {
			var image = new Image();

			camstatus = 1;
			dataURL = reader.result;
			drawCanvas();
			if (typeof imgclick.last != 'undefined')
				document.getElementById("capture").disabled = false;
		}, false);

		reader.readAsDataURL(file);
	});

	captureButton.addEventListener('click', () => {
		document.getElementById("capture").disabled = true;
		if (typeof imgclick.last != 'undefined') {
			// Draw the video frame to the canvas.
			canvassnap.getContext('2d').drawImage(video, 0, 0);
			if (document.getElementById("fileInput").style.display == "none")
				var txt = "mid=" + imgclick.last + "&cam=" + encodeURIComponent(canvassnap.toDataURL());
			else
				var txt = "mid=" + imgclick.last + "&cam=" + encodeURIComponent(dataURL);
			$.ajax({
				url: '/meme.php',
				type: 'post',
				data: txt,
				success: function (data) {
					//console.log(data);
					window.location.reload();
				}
			});
		}
	});


}

