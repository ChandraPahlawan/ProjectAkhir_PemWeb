        // scroll ke bagian yang diinginkan
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
          anchor.addEventListener('click', function (e) {
              e.preventDefault();

      const targetElement = document.querySelector(this.getAttribute('href'));
      if (targetElement) {
          targetElement.scrollIntoView({
              behavior: 'smooth'
              });
          }
      });
      });

      // Script untuk mengontrol scroll daftar film
      function scrollLeftList(element) {
          const movieList = element.parentElement.querySelector('.movie-list');
          const scrollAmount = Math.min(300, movieList.scrollLeft);
          movieList.scrollBy({ left: -scrollAmount, behavior: 'smooth' });
      }

      function scrollRightList(element) {
          const movieList = element.parentElement.querySelector('.movie-list');
          movieList.scrollBy({ left: 300, behavior: 'smooth' });
      }
      
    function toggleComment(reviewID) {
        const comment = document.getElementById(`comment-${reviewID}`);
        const readMore = comment.nextElementSibling;

        if (comment.classList.contains('expanded')) {
            comment.classList.remove('expanded');
            comment.classList.add('collapsed');
            readMore.innerText = 'Read More';
            comment.innerText = comment.getAttribute('data-short-text');
        } else {
            comment.classList.remove('collapsed');
            comment.classList.add('expanded');
            readMore.innerText = 'Read Less';
            comment.innerText = comment.getAttribute('data-full-text');
        }
    }

// function showFullDescription(button) {
//     const fullDescription = button.nextElementSibling;
//     if (fullDescription.style.display === 'none') {
//         fullDescription.style.display = 'inline';
//         button.style.display = 'none';
//     }
// }