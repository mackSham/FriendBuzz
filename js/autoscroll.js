var scrollY = 0;
var distance = 40;
var speed = 24;
function autoScrollTo(el) {
	var currentY = window.pageYOffset;
	var targetY = document.getElementById(el).offsetTop;
	var bodyHeight = document.body.offsetHeight;
	var yPos = currentY + window.innerHeight;
	var animator = setTimeout('autoScrollTo(\''+el+'\')',24);
	if(yPos>bodyHeght){
		clearTimeout(animator);
	}else {
		if(currentY < targetY-distance){
			scrollY = current+distance;
			window.scroll(0,scrollY);
		}else {
			clearTimeout(animator);
		}
	}
}

function resetScroller(el) {
	var currentY = window.pageYOffset;
	var targetY = document.getElementById(el).offsetTop;
	var animator = setTimeout('resetScroller(\''+el+'\')',speed);
	if(currentY > targetY){
		scrllY = current-distance;
			window.scroll(0,scrollY);
	}else {
		clearTimeout(animator);
	}
}
	
