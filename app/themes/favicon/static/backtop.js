document.addEventListener('scroll', function() {
      var backToTopButton = document.getElementById('back-to-top');
      if (window.pageYOffset > 100) {
          backToTopButton.style.display = 'block';
      } else {
          backToTopButton.style.display = 'none';
      }
  });

  document.getElementById('back-to-top').addEventListener('click', function() {
      window.scrollTo({top: 0, behavior: 'smooth'});
  });