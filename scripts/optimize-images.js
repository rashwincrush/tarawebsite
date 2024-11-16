const sharp = require('sharp');
const fs = require('fs');
const path = require('path');

// Configuration
const QUALITY = 80; // Adjust quality (0-100)
const MAX_WIDTH = 1200; // Maximum width for large images
const THUMBNAIL_WIDTH = 300; // Width for thumbnails

async function optimizeImage(inputPath, outputPath, options = {}) {
    const { width = MAX_WIDTH, quality = QUALITY } = options;
    
    try {
        await sharp(inputPath)
            .resize(width, null, {
                fit: 'inside',
                withoutEnlargement: true
            })
            .webp({ quality }) // Convert to WebP format
            .toFile(outputPath);
            
        console.log(`Optimized: ${path.basename(inputPath)} -> ${path.basename(outputPath)}`);
    } catch (error) {
        console.error(`Error processing ${inputPath}:`, error);
    }
}

async function processDirectory(inputDir) {
    // Create optimized directory if it doesn't exist
    const optimizedDir = path.join(path.dirname(inputDir), 'optimized');
    if (!fs.existsSync(optimizedDir)) {
        fs.mkdirSync(optimizedDir, { recursive: true });
    }

    const files = fs.readdirSync(inputDir);

    for (const file of files) {
        const inputPath = path.join(inputDir, file);
        const stats = fs.statSync(inputPath);

        if (stats.isDirectory()) {
            // Skip the optimized directory to prevent infinite recursion
            if (path.basename(inputPath) !== 'optimized') {
                await processDirectory(inputPath);
            }
        } else if (/\.(jpg|jpeg|png|gif)$/i.test(file)) {
            // Process image files
            const filename = path.parse(file).name;
            
            // Create WebP version
            const webpPath = path.join(optimizedDir, `${filename}.webp`);
            await optimizeImage(inputPath, webpPath);
            
            // Create thumbnail version
            const thumbnailPath = path.join(optimizedDir, `${filename}-thumb.webp`);
            await optimizeImage(inputPath, thumbnailPath, { 
                width: THUMBNAIL_WIDTH,
                quality: QUALITY
            });
        }
    }
}

// Directory paths
const imagesDir = path.join(__dirname, '..', 'images');

// Run optimization
processDirectory(imagesDir)
    .then(() => console.log('Image optimization complete!'))
    .catch(error => console.error('Error:', error));
