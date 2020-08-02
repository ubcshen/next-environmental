export default {
  init() {
    // JavaScript to be fired on all pages
    $('.lazyImg').unveil(); 
    $('.hamburger').on('click', function () {
      $(this).toggleClass('open');
    });
  },
  finalize() {
    // JavaScript to be fired on all pages, after page specific JS is fired
  },
};
