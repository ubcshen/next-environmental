export default {
  init() {
    // JavaScript to be fired on all pages
    $('.lazyImg').unveil(); 
  },
  finalize() {
    // JavaScript to be fired on all pages, after page specific JS is fired
  },
};