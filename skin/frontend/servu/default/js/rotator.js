$j(document).ready(function(){
  var currentPosition = 1;
  var slideHeight = $j('#slideshow').height();
  var slideWidth = $j('#slideshow #slidesContainer .slide').width();
  var slides = $j('.slide');
  var numberOfSlides = slides.length; 
  var interval = 0;
  
  $j('#slideInner .slide').css('visibility', 'visible');
  $j('#slidesContainer').css('overflow', 'hidden');

  slides
  .wrapAll('<div id="slideInner"></div>')
  
  .css({
    'height' : slideHeight,
    'visibility' : 'visible'
  });

  $j('#slideInner').css('width', slideWidth * numberOfSlides);
 
  for( i=1; i < numberOfSlides+1; i++){
   $j('#slideshow').append('<span class="control" id="'+ i +'">'+ i +'</span>');
  }
  
  $j('#slideshow').append("<span class='pause'>Pause</span> ");
  $j('#slideshow').append("<span class='play' style='display:none;'>Play</span> ");
  
  $j('#slideshow #'+ currentPosition).addClass("current")

  $j('.control')
    .bind('click', function(){
        
        currentPosition = ($j(this).attr('id'))
      
        $j("#slideshow span.current").removeClass("current")
      
        $j(this).addClass("current");
        
        animateSlide();
        
        if(interval != 0){
            stopInterval();
            startInterval();
        }
    });
    
    $j('#slideshow .play').click(function(){
        $j('#slideshow .play').hide();
	$j('#slideshow .pause').show();
        startInterval();
    });
    
   $j('#slideshow .pause').click(function(){
        $j('#slideshow .play').show();
	$j('#slideshow .pause').hide();
        stopInterval();
    });
    
    startInterval();
    
    
    
    function startInterval(){
        if(interval == 0){
          interval = setInterval(automaticSlide,10000)
        }else{
          stopInterval()
        }
    }

    function stopInterval(){
        if(interval != 0){
          clearInterval(interval)
          interval=0
      }
    }
    
    function automaticSlide() {
        currentPosition++;
        if(currentPosition == numberOfSlides+1) {
            currentPosition = 1;
   	}
   		
	$j("#slideshow span.current").removeClass("current");
     	$j("#slideshow #"+currentPosition).addClass("current");

      	animateSlide();
    }
    
    function animateSlide(){
        $j('#slideInner').animate({
            'marginLeft' : slideWidth*(-(currentPosition-1))
        });
    }
    
  });