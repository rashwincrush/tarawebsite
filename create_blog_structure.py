#!/usr/bin/env python3
import os
import shutil

def create_directory(path):
    """Create directory if it doesn't exist"""
    if not os.path.exists(path):
        os.makedirs(path)
        print(f"Created directory: {path}")
    else:
        print(f"Directory already exists: {path}")

def create_file(path, content=""):
    """Create file with optional content if it doesn't exist"""
    if not os.path.exists(path):
        with open(path, 'w') as f:
            f.write(content)
        print(f"Created file: {path}")
    else:
        print(f"File already exists: {path}")

def setup_blog_structure():
    # Base project directory is current directory
    base_dir = os.getcwd()
    
    # Define the directory structure
    directories = [
        'assets/img/blog/thumbnails',
        'assets/img/blog/content',
        'blog/posts',
        'blog/categories',
        'blog/assets/css',
        'blog/assets/js'
    ]
    
    # Create directories
    for directory in directories:
        create_directory(os.path.join(base_dir, directory))
    
    # Create necessary blog files
    files_to_create = {
        'blog/index.html': '<!-- Main blog listing page -->',
        'blog/posts/dental-care.html': '<!-- Dental Care blog post -->',
        'blog/posts/oral-hygiene.html': '<!-- Oral Hygiene blog post -->',
        'blog/posts/treatments.html': '<!-- Treatments blog post -->',
        'blog/categories/dental-care.html': '<!-- Dental Care category page -->',
        'blog/categories/oral-hygiene.html': '<!-- Oral Hygiene category page -->',
        'blog/categories/treatments.html': '<!-- Treatments category page -->',
        'blog/assets/css/blog.css': '/* Blog-specific styles */',
        'blog/assets/js/blog-list.js': '// Blog listing functionality',
        'blog/assets/js/blog-post.js': '// Blog post functionality'
    }
    
    for file_path, content in files_to_create.items():
        create_file(os.path.join(base_dir, file_path), content)

def verify_structure():
    """Verify the created structure"""
    print("\nVerifying directory structure:")
    for root, dirs, files in os.walk('.'):
        # Skip .git directory
        if '.git' in dirs:
            dirs.remove('.git')
        
        level = root.replace('.', '').count(os.sep)
        indent = ' ' * 4 * level
        print(f"{indent}{os.path.basename(root)}/")
        subindent = ' ' * 4 * (level + 1)
        for file in files:
            if not file.startswith('.'):  # Skip hidden files
                print(f"{subindent}{file}")

if __name__ == "__main__":
    print("Setting up blog directory structure...")
    setup_blog_structure()
    print("\nDirectory structure setup complete!")
    verify_structure()
    print("\nBlog structure has been created successfully!")