#!/bin/bash

# Create main directories
mkdir -p assets/{css,js,images/{blog,testimonials,general},fonts}
mkdir -p blog/{categories,posts}
mkdir -p patient-resources/forms
mkdir -p admin/{blog-manager,media-manager,testimonial-manager}
mkdir -p includes
mkdir -p api/{forms,social,reviews}
mkdir -p seo

# Create CSS files
touch assets/css/style.css
touch assets/css/blog.css
touch assets/css/patient-resources.css
touch assets/css/responsive.css

# Create JavaScript files
touch assets/js/main.js
touch assets/js/blog-manager.js
touch assets/js/forms-handler.js
touch assets/js/social-feed.js
touch assets/js/reviews-widget.js

# Create HTML files
touch blog/index.html
touch patient-resources/forms/new-patient.html
touch patient-resources/forms/appointment.html
touch patient-resources/forms/feedback.html
touch patient-resources/faq.html
touch patient-resources/testimonials.html

# Create include files
touch includes/header.html
touch includes/footer.html
touch includes/social-feed.html
touch includes/review-widget.html

# Create SEO files
touch seo/sitemap.xml
touch seo/robots.txt

# Create root HTML files (if they don't exist)
touch index.html
touch about.html
touch services.html
touch contact.html

# Create .gitignore if it doesn't exist
touch .gitignore
touch README.md