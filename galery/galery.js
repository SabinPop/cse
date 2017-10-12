window.addEventListener("keydown", checkKeyPressed, false);
color = 0;

    var pagePositon = 0,
        sectionsSeclector = 'section',
        $scrollItems = $(sectionsSeclector),
        offsetTolorence = 30,
        pageMaxPosition = $scrollItems.length - 1;

    //Map the sections:
    $scrollItems.each(function(index,ele) { $(ele).attr("debog",index).data("pos",index); });

    // Bind to scroll
    $(window).bind('scroll',upPos);

    //Update position func:
    function upPos(){
       var fromTop = $(this).scrollTop();
       var $cur = null;
        $scrollItems.each(function(index,ele){
            if ($(ele).offset().top < fromTop + offsetTolorence) $cur = $(ele);
        });
       if ($cur != null && pagePositon != $cur.data('pos')) {
           pagePositon = $cur.data('pos');
       }
    }
if(color == 0){
  $(".logo").addClass('low-brightness');
}
if(color == 2){
  $(".logo").removeClass('high-brightness');
  $(".logo").addClass('low-brightness');
}
if(color == 1){
  $(".logo").removeClass('low-brightness');
  $(".logo").addClass('high-brightness');
}

$('#keyboard').popover({
  trigger: 'focus'
});

$( "body" ).dblclick(function() {
  theme('.page-scroll');
});

function checkKeyPressed(e) {
    if (e.keyCode == "68" || e.keyCode == "100") {
        dark();
    }
	if(e.keyCode == "76" || e.keyCode == "108"){
		white();
	}
	if(e.keyCode == "75" || e.keyCode == "96"){
		if (pagePositon-1 >= 0) {
            pagePositon--;
            $('html, body').stop().animate({
                  scrollTop: $scrollItems.eq(pagePositon).offset().top
              }, 300);
            return false;
        }
	}
	if(e.keyCode == "74" || e.keyCode == "95"){
		if (pagePositon+1 <= pageMaxPosition) {
            pagePositon++;
            $('html, body').stop().animate({
                  scrollTop: $scrollItems.eq(pagePositon).offset().top
            }, 300);
        }
	}
	if(e.keyCode == "65" || e.keyCode == "97"){
		window.location = "../cse/admin/";
	}
}
function theme(button){
	if(color == 2){
		dark();
	}
	else if(color == 1){
		white();
	}
	else{
		dark();
	}
}

function dark() {
  color = 1;
  $('.panel-heading').css('background-color', '#999999');
  $('.panel-body').css('background-color', '#333333');
  $('.panel-footer').css('background-color', '#555555');
  $("theme").addClass('white');
  $("p").css('color', '#FFFFFF');
	$(".navbar-fixed-top").css('background-color', '#333333');
	$(".inside-block").css('background-color', 'rgba(51, 51, 51, 0.7)');
	$(".inside-block").css('color', '#FFFFFF');
	$("body").css("background-color", "#333333");
	$(".navbar-fixed-top").css("border-bottom-size", "1px");
	$(".navbar-fixed-top").css("border-bottom-color", "#e7e7e7");
	$(".page-scroll").css("color", "#FFFFFF");
	document.body.style.color = "#FFFFFF";
}

function white() {
  color = 2;
  $('.panel-heading').css('background-color', '#F5F5F5');
  $('.panel-body').css('background-color', '#FFFFFF');
  $('.panel-footer').css('background-color', '#F5F5F5');
  $("theme").addClass('dark');
  $("p").css('color', 'rgba(0, 0, 0, 0.7)');
  $("dark").css("background-color", "#333333");
    $(".navbar-fixed-top").css('background-color', '#FFFFFF');
	$(".inside-block").css('background-color', 'rgba(255, 255, 255, 0.7)');
	$(".inside-block").css('color', '#000000');
	$(".page-scroll").css("color", "#333333");
    $("body").css("background-color", "#FFFFFF");
    $(".navbar-fixed-top").css("border-bottom-size", "1px");
    $(".navbar-fixed-top").css("border-bottom-color", "#e7e7e7");
    document.body.style.color = "#000000";
}

function openModal() {
  document.getElementById('myModal').style.display = "block";
}

function closeModal() {
  document.getElementById('myModal').style.display = "none";
}

var slideIndex = 1;
showSlides(slideIndex);

function plusSlides(n) {
  showSlides(slideIndex += n);
}

function currentSlide(n) {
  showSlides(slideIndex = n);
}

function showSlides(n) {
	window.onclick = function(event) {
		if (event.target == document.getElementById('myModal')) {
			document.getElementById('myModal').style.display = "none";
		}
	}
  var i;
  var slides = document.getElementsByClassName("mySlides");
  var dots = document.getElementsByClassName("demo");
  var captionText = document.getElementById("caption");
  if (n > slides.length) {slideIndex = 1}
  if (n < 1) {slideIndex = slides.length}
  for (i = 0; i < slides.length; i++) {
      slides[i].style.display = "none";
  }
  for (i = 0; i < dots.length; i++) {
      dots[i].className = dots[i].className.replace(" active", "");
  }
  slides[slideIndex-1].style.display = "block";
  dots[slideIndex-1].className += " active";
  captionText.innerHTML = dots[slideIndex-1].alt;

  $(function() {

  var native_width = 0;
  var native_height = 0;
  var mouse = {x: 0, y: 0};
  var magnify;
  var cur_img;

  var ui = {
    magniflier: $('.magniflier')
  };

  // Add the magnifying glass
  if (ui.magniflier.length) {
    var div = document.createElement('div');
    div.setAttribute('class', 'glass');
    ui.glass = $(div);

    $('body').append(div);
  }


  // All the magnifying will happen on "mousemove"

  var mouseMove = function(e) {
    var $el = $(this);

    // Container offset relative to document
    var magnify_offset = cur_img.offset();

    // Mouse position relative to container
    // pageX/pageY - container's offsetLeft/offetTop
    mouse.x = e.pageX - magnify_offset.left;
    mouse.y = e.pageY - magnify_offset.top;

    // The Magnifying glass should only show up when the mouse is inside
    // It is important to note that attaching mouseout and then hiding
    // the glass wont work cuz mouse will never be out due to the glass
    // being inside the parent and having a higher z-index (positioned above)
    if (
      mouse.x < cur_img.width() &&
      mouse.y < cur_img.height() &&
      mouse.x > 0 &&
      mouse.y > 0
      ) {

      magnify(e);
    }
    else {
      ui.glass.fadeOut(100);
    }

    return;
  };

  var magnify = function(e) {

    var rx = Math.round(mouse.x/cur_img.width()*native_width - ui.glass.width()/2)*-1;
    var ry = Math.round(mouse.y/cur_img.height()*native_height - ui.glass.height()/2)*-1;
    var bg_pos = rx + "px " + ry + "px";

    // var glass_left = mouse.x - ui.glass.width() / 2;
    // var glass_top  = mouse.y - ui.glass.height() / 2;
    var glass_left = e.pageX - ui.glass.width() / 2;
    var glass_top  = e.pageY - ui.glass.height() / 2;
    //console.log(glass_left, glass_top, bg_pos)
    // Now, if you hover on the image, you should
    // see the magnifying glass in action
    ui.glass.css({
      left: glass_left,
      top: glass_top,
      backgroundPosition: bg_pos
    });

    return;
  };

  $('.magniflier').on('mousemove', function() {
    ui.glass.fadeIn(100);

    cur_img = $(this);

    var large_img_loaded = cur_img.data('large-img-loaded');
    var src = cur_img.data('large') || cur_img.attr('src');

    // Set large-img-loaded to true
    // cur_img.data('large-img-loaded', true)

    if (src) {
      ui.glass.css({
        'background-image': 'url(' + src + ')',
        'background-repeat': 'no-repeat'
      });
    }

      if (!cur_img.data('native_width')) {
        // This will create a new image object with the same image as that in .small
        // We cannot directly get the dimensions from .small because of the
        // width specified to 200px in the html. To get the actual dimensions we have
        // created this image object.
        var image_object = new Image();

        image_object.onload = function() {
          // This code is wrapped in the .load function which is important.
          // width and height of the object would return 0 if accessed before
          // the image gets loaded.
          native_width = image_object.width;
          native_height = image_object.height;

          cur_img.data('native_width', native_width);
          cur_img.data('native_height', native_height);

          //console.log(native_width, native_height);

          mouseMove.apply(this, arguments);

          ui.glass.on('mousemove', mouseMove);

        };


        image_object.src = src;

        return;
      } else {

        native_width = cur_img.data('native_width');
        native_height = cur_img.data('native_height');
      }
    //}
    //console.log(native_width, native_height);

    mouseMove.apply(this, arguments);

    ui.glass.on('mousemove', mouseMove);
  });

  ui.glass.on('mouseout', function() {
    ui.glass.off('mousemove', mouseMove);
  });

});
}
