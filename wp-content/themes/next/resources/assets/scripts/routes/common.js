export default {
  init() {
    // JavaScript to be fired on all pages
    $('.lazyImg').unveil(); 
    $('.hamburger').on('click', function () {
      $(this).toggleClass('open');
      //$(window).trigger('scroll');
      //$('.casestudy-image').trigger('unveil');
      setTimeout(function () {
      $('.casestudy-image').unveil(200);
      }, 0);
    });
    var previousScroll = 0;
    $(window).scroll(function () {
      var currentScroll = $(this).scrollTop();
      if (currentScroll < 100) {
        showTopNav();
      } else if (currentScroll >= 100 && currentScroll < $(document).height() - $(window).height()) {
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
    $('.svg-arrow').waypoint({
      handler: function(direction) {
        if (direction === 'down') { 
          $(this.element).addClass('animate__animated animate__slideInLeft animate__slower');
        }
      },
      offset: 250,
    });

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
      offset: 250,
    });
  },
};
