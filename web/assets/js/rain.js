// Rain Animation
var canvas = $('#canvas')[0];
canvas.width = window.innerWidth;
canvas.height = window.innerHeight;

var ctx = canvas.getContext('2d');
var w = canvas.width;
var h = canvas.height;
ctx.strokeStyle = 'rgba(63,139,222,0.20)';
ctx.lineWidth = 1;
ctx.lineCap = 'round';


var init = [];
var maxParts = 50;
for (var a = 0; a < maxParts; a++) {
	init.push({
		x: Math.random() * (w + 100),
		y: Math.random() * h,
		l: 5 + Math.random() * 5,
		xs: -2 * Math.random() - 5,
		ys: Math.random() * 10 + 20
	})
}

var particles = [];
for (var b = 0; b < maxParts; b++) {
	particles[b] = init[b];
}

function draw() {
	ctx.clearRect(0, 0, w, h);
	for (var c = 0; c < particles.length; c++) {
		var p = particles[c];
		ctx.beginPath();
		ctx.moveTo(p.x, p.y);
		ctx.lineTo(p.x + p.l * p.xs, p.y + p.l * p.ys);
		ctx.stroke();
	}
	move();
}

function move() {
	for (var b = 0; b < particles.length; b++) {
		var p = particles[b];
		p.x += p.xs;
		p.y += p.ys;
		if (p.x > w || p.y > h) {
			p.x = Math.random() * w;
			p.y = -20;
		}
	}
}

function render() {
	draw();
	requestAnimationFrame(render);
}

render();