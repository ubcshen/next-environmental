export default {
  init() {
    // JavaScript to be fired on the home page
    $('.counter').countUp({
      'time': 2000,
      'delay': 10,
    });
  },
  finalize() {
    // JavaScript to be fired on the home page, after the init JS
  },
};
