// blog/assets/js/blog-list.js

document.addEventListener('DOMContentLoaded', function() {
    // Get DOM elements
    const searchInput = document.getElementById('blogSearchInput');
    const blogGrid = document.getElementById('blogGrid');
    const categoryButtons = document.querySelectorAll('.blog-category-tag');

    // Search functionality
    searchInput?.addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase();
        const blogPosts = blogGrid.querySelectorAll('.col-md-4');

        blogPosts.forEach(post => {
            const title = post.querySelector('.heading').textContent.toLowerCase();
            const content = post.querySelector('p').textContent.toLowerCase();
            
            if (title.includes(searchTerm) || content.includes(searchTerm)) {
                post.style.display = '';
            } else {
                post.style.display = 'none';
            }
        });
    });

    // Category filtering
    categoryButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Remove active class from all buttons
            categoryButtons.forEach(btn => btn.classList.remove('active'));
            // Add active class to clicked button
            this.classList.add('active');

            const category = this.getAttribute('data-category');
            const blogPosts = blogGrid.querySelectorAll('.col-md-4');

            blogPosts.forEach(post => {
                if (category === 'all') {
                    post.style.display = '';
                } else {
                    const postCategory = post.querySelector('.blog-entry').getAttribute('data-category');
                    post.style.display = postCategory === category ? '' : 'none';
                }
            });
        });
    });
});