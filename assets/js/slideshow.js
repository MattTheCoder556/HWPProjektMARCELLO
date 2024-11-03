let slideIndex = 1;
let slideChangeDelay = 3000; // Set the delay to 3000 milliseconds (3 seconds)

showSlides(slideIndex);

// Next/previous controls
function plusSlides(n) {
  clearTimeout(autoChange); // Clear the automatic change timeout
  showSlides(slideIndex += n);
  setTimeout(() => showSlides(slideIndex += 1), slideChangeDelay); // Set a delay before auto-changing
}

// Thumbnail image controls
function currentSlide(n) {
  clearTimeout(autoChange); // Clear the automatic change timeout
  showSlides(slideIndex = n);
  setTimeout(() => showSlides(slideIndex += 1), slideChangeDelay); // Set a delay before auto-changing
}

function showSlides(n) {
  let i;
  let slides = document.getElementsByClassName("mySlides");
  let dots = document.getElementsByClassName("dot");
  
  if (n > slides.length) {slideIndex = 1}
  if (n < 1) {slideIndex = slides.length}

  for (i = 0; i < slides.length; i++) {
    slides[i].style.display = "none";  // Hide all slides
    slides[i].classList.remove("slide"); // Remove slide class for the new animation
  }
  
  for (i = 0; i < dots.length; i++) {
    dots[i].className = dots[i].className.replace(" active", "");  // Remove active class from all dots
  }

  slides[slideIndex - 1].style.display = "block";  // Show the current slide
  slides[slideIndex - 1].classList.add("slide"); // Add slide animation class
  dots[slideIndex - 1].className += " active";  // Set the current dot to active

  // Automatically change to the next slide after the specified delay
  autoChange = setTimeout(() => showSlides(slideIndex += 1), slideChangeDelay);
}
