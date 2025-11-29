document.addEventListener('DOMContentLoaded', () => {
    let currentSlide = 0;
    const items = document.querySelectorAll('.carousel-item');
    const dots = document.querySelectorAll('.dot');
    const totalItems = items.length;

    // Function to change slide
    const changeSlide = () => {
        // Step 1: Fade out the current slide
        items[currentSlide].classList.remove('active');
        dots[currentSlide].classList.remove('active');

        setTimeout(() => {
            // Hide the current slide after the fade-out transition
            items[currentSlide].style.display = 'none';

            // Step 2: Move to the next slide
            currentSlide = (currentSlide + 1) % totalItems;

            // Show the next slide
            items[currentSlide].style.display = 'flex';
            
            setTimeout(() => {
                // Step 3: Fade in the next slide
                items[currentSlide].classList.add('active');
                dots[currentSlide].classList.add('active');
            }, 10); // Short delay to allow display change before fade-in
        }, 500); // Duration matching the CSS transition time
    };

    // Automatically change slide every 3 seconds
    setInterval(changeSlide, 3000);

    // Initially display the first slide
    items[currentSlide].style.display = 'flex';
    items[currentSlide].classList.add('active');
});