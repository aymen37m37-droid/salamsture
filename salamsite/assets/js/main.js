// Main JavaScript File
document.addEventListener('DOMContentLoaded', function() {
    // Add interactive functionality here
    console.log('Website loaded successfully');
    
    // Example: Add smooth scrolling to navigation links
    const navLinks = document.querySelectorAll('nav a');
    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const targetId = this.getAttribute('href');
            if (targetId === '#') {
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            }
        });
    });
});