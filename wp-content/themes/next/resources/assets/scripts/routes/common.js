export default {
  init() {
    // JavaScript to be fired on all pages
    $('.lazyImg').unveil(); 
    $('.hamburger').on('click', function () {
      $(this).toggleClass('open');
      $(this).closest('.header-banner').toggleClass('open');
      //$(window).trigger('scroll');
      //$('.casestudy-image').trigger('unveil');
      setTimeout(function () {
      $('.casestudy-image').unveil(200);
      }, 0);
    });
    // var window.isMobile = /iphone|ipod|ipad|android|blackberry|opera mini|opera mobi|skyfire|maemo|windows phone|palm|iemobile|symbian|symbianos|fennec/i.test(navigator.userAgent.toLowerCase());
    var previousScroll = 0;
    $(window).scroll(function () {
      var currentScroll = $(this).scrollTop();
      if (currentScroll < 100) {
        showTopNav();
      } else if (currentScroll >= 100 && currentScroll < $(document).height() - $(window).height()) { // && !$('.navbar-collapse').hasClass('show')
        if (currentScroll > previousScroll) {
          hideNav();
          if($('.additional-nav').hasClass('show')) { $('.hamburger').click(); }
        } else {
          showNav();
        }
        previousScroll = currentScroll;
      }
    });

    function hideNav() {
      $('.header-banner').removeClass('is-visible').addClass('is-hidden fixed-top');
      $('.hide-scroll, .menu-topmenutwo-container').removeClass('is-visible').addClass('is-hidden');
      $('.hide-scroll, .menu-topmenutwo-container').removeClass('d-flex').addClass('d-none');
    }

    function showNav() {
      $('.header-banner').removeClass('is-hidden').addClass('fixed-top').addClass('is-visible');
      //$('.hide-scroll').removeClass('is-hidden').addClass('is-visible').addClass('scrolling');
    }

    function showTopNav() {
      $('.header-banner').removeClass('is-hidden fixed-top').addClass('is-visible').addClass('scrolling');
      $('.hide-scroll, .menu-topmenutwo-container').removeClass('is-hidden').addClass('is-visible');
      $('.hide-scroll, .menu-topmenutwo-container').removeClass('d-none').addClass('is-visible').addClass('d-flex');
    }

    $(window).scroll(function() {
      var maxHeight = document.body.scrollHeight;
      if ($(this).scrollTop() > maxHeight * 0.65) {
      $('#toTopBtn').fadeIn();
      } else {
      $('#toTopBtn').fadeOut();
      }
    });

    $('#toTopBtn').click(function() {
      $('html, body').animate({scrollTop: 0}, 1000);
      return false;
    });
  },
  finalize() {
    // JavaScript to be fired on all pages, after page specific JS is fired
    /*$('.section-chat').waypoint({
      handler: function(direction) {
        if (direction === 'down') { 
          $(this.element).find('.svg-arrow').addClass('animate__animated animate__slideInLeft animate__slower');
        }
      },
      offset: function() {
        var chatPoint = $('.section-chat').outerHeight();
        return chatPoint*2+50
      },
    });

    $('.svg-chat-arrow').waypoint({
      handler: function(direction) {
        if (direction !== 'up') { 
          $(this.element).addClass('animate__animated animate__slideInLeft animate__slower');
        }
      },
    });*/

    $('.imgAnimation').addClass('animate__animated animate__fadeInLeft animate__slower');

    $('.svg-arrow-headline').waypoint({
      handler: function(direction) {
        if (direction === 'down') { 
          $(this.element).addClass('animate__animated animate__slideInLeft animate__slower');
        }
      },
      offset: 250,
    });

    $('.svg-arrow-case-study').waypoint({
      handler: function(direction) {
        if (direction === 'down') { 
          $(this.element).addClass('animate__animated animate__slideInLeft animate__slower');
        }
      },
      offset: function() {
        var chatPoint = $('.section-case-study').outerHeight();
        return chatPoint+50
      },
    });

    $('.svg-arrow-testimonial').waypoint({
      handler: function(direction) {
        if (direction === 'down') { 
          $(this.element).addClass('animate__animated animate__slideInLeft animate__slow');
        }
      },
      offset: function() {
        var chatPoint = $('.svg-arrow-testimonial').outerHeight();
        return chatPoint*2+200
      },
    });

    $('.svg-arrow-head-home').addClass('animate__animated animate__slideInLeft animate__slower');

    $('.mobile-dropdown').select2({
      minimumResultsForSearch: -1,
    });

    $('.mobile-dropdown-risk').select2({
      minimumResultsForSearch: -1,
    });

    $('.mobile-dropdown').on('select2:select', function (e) {
      var data = e.params.data;
      $('.tab-pane').removeClass('show active');
      $('.title-detail').removeClass('show active');
      $(data['id']).addClass('show active');
      $('#title-'+data['id'].split('#')[1]).addClass('show active');
    });

    $('.mobile-dropdown-risk').on('select2:select', function (e) {
      var data = e.params.data;
      $('.tab-pane').removeClass('show active');
      $(data['id']).addClass('show active');
      $('.risk-pointer').attr('id',data['id']);
      $('.risk-tab-container .nav-item').each(function() {
        if($(this).attr('data-pointer') === data['id']) {
          $(this).click();
        }
      });
    });

    //price page
    $('.risk-tab-container .nav-item').click(function(e) {
      e.preventDefault();
      $('.risk-pointer').attr('id',$(this).attr('data-pointer'));
      $('.mobile-dropdown-risk').val($(this).attr('data-pointer'));
      $('.mobile-dropdown-risk').trigger('change'); 
    });


    if(document.getElementById('myline')) {
      // Find scroll percentage on scroll (using cross-browser properties), and offset dash same amount as percentage scrolled
      window.addEventListener('scroll', myFunction);
    }

    function myFunction() {
      var myline = document.getElementById('myline');
      var length = myline.getTotalLength();
      var circle = document.getElementById('circle');
      length = $('.icon-line').height();
      //console.log('length: ' + $('.icon-line').height());
      myline.setAttribute('d', 'M165 0 v' + length +' 20');
      //console.log('length1: ' + myline.getAttribute('d'));
      // The start position of the drawing
      myline.style.strokeDasharray = length;

      // Hide the triangle by offsetting dash. Remove this line to show the triangle before scroll draw
      myline.style.strokeDashoffset = length;
      // What % down is it?
      var scrollpercent = (document.body.scrollTop + document.documentElement.scrollTop) / (document.documentElement.scrollHeight - document.documentElement.clientHeight);
      // Length to offset the dashes
      var draw = length * scrollpercent*5;

      // Reverse the drawing (when scrolling upwards)
      myline.style.strokeDashoffset = ((length - draw) >= 0 ) ? length - draw : 0;

      //get point at length
      var endPoint = myline.getPointAtLength(draw);
      endPoint.y = (endPoint.y <= length ) ? endPoint.y : length;
      circle.setAttribute('cx', endPoint.x);
      circle.setAttribute('cy', endPoint.y);
    }
      

    $( window ).resize(function() {
      var myline = document.getElementById('myline');
      if(myline) {
        var length = myline.getTotalLength();
        length = $('.icon-line').height();
        myline.setAttribute('d', 'M165 0 v' + length +' 20');
        // The start position of the drawing
        myline.style.strokeDasharray = length;
        // Hide the triangle by offsetting dash. Remove this line to show the triangle before scroll draw
        myline.style.strokeDashoffset = length;
        myFunction();

        // Find scroll percentage on scroll (using cross-browser properties), and offset dash same amount as percentage scrolled
        window.addEventListener('scroll', myFunction);
      }
    });
  },
};
