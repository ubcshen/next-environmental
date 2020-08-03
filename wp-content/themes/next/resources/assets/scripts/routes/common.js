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
  },
  finalize() {
    // JavaScript to be fired on all pages, after page specific JS is fired
  },
};
