<?php
require_once 'includes/functions.php';

// File/Folder Size Calculator
function calculateSize($path) {
    // Security check - only allow paths within the site root
    $rootPath = realpath(__DIR__);
    $requestedPath = realpath($rootPath . '/' . $path);
    
    // Check if the requested path is within the site root
    if ($requestedPath === false || strpos($requestedPath, $rootPath) !== 0) {
        return array('error' => 'Access denied. Path must be within the site directory.');
    }
    
    if (!file_exists($requestedPath)) {
        return array('error' => 'File or directory does not exist.');
    }
    
    if (is_file($requestedPath)) {
        $size = filesize($requestedPath);
        return array(
            'type' => 'file',
            'name' => basename($requestedPath),
            'size' => $size,
            'formatted_size' => formatFileSize($size)
        );
    } elseif (is_dir($requestedPath)) {
        $size = getDirSize($requestedPath);
        return array(
            'type' => 'directory',
            'name' => basename($requestedPath),
            'size' => $size,
            'formatted_size' => formatFileSize($size),
            'file_count' => countFiles($requestedPath)
        );
    }
    
    return array('error' => 'Invalid path type.');
}

function getDirSize($dir) {
    $size = 0;
    if (is_dir($dir)) {
        $files = scandir($dir);
        foreach ($files as $file) {
            if ($file != '.' && $file != '..') {
                $path = $dir . '/' . $file;
                if (is_file($path)) {
                    $size += filesize($path);
                } elseif (is_dir($path)) {
                    $size += getDirSize($path);
                }
            }
        }
    }
    return $size;
}

function countFiles($dir) {
    $count = 0;
    if (is_dir($dir)) {
        $files = scandir($dir);
        foreach ($files as $file) {
            if ($file != '.' && $file != '..') {
                $path = $dir . '/' . $file;
                if (is_file($path)) {
                    $count++;
                } elseif (is_dir($path)) {
                    $count += countFiles($path);
                }
            }
        }
    }
    return $count;
}

function formatFileSize($bytes) {
    $units = array('B', 'KB', 'MB', 'GB', 'TB');
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    $bytes /= pow(1024, $pow);
    return round($bytes, 2) . ' ' . $units[$pow];
}

// Process size calculation request
$sizeResult = null;
if (isset($_GET['path']) && !empty($_GET['path'])) {
    $sizeResult = calculateSize($_GET['path']);
}

include 'includes/header.php';
?>

<main class="main--about-us">
    <div class="main-wrapper--about-us">
        <h2 class="main--about-us__title">
            Our True Story
            <img class="title-sub-logo"
                src="pictures/icons/sub-logo.svg"
                width="36px"
                height="36px"
            />
        </h2>
        <p class="main--about-us__story-text">
            <span class="span-bullet">
                <img src="pictures/icons/bullet.svg" alt="" height="16px" width="16px"/>
            </span>
            <span class="text-first-letter">O</span>nce upon a time, in a quaint corner of a bustling city, there stood
            the Borodinski barbershop, a paradise of beauty and transformation. The building's
            facade boasted vintage charm, and the weathered sign gently swayed
            in the wind. The aroma of sandalwood and lavender filled the air,
            attracting passersby with promises of magical experiences.<br /><br />
            
            <span class="span-bullet">
                <img src="pictures/icons/bullet.svg" alt="" />
            </span>
            <span class="text-first-letter">I</span>nside, the barbershop was a charming refuge from the outside
            world. The walls were adorned with vintage photographs and antique
            mirrors that seemed to whisper secrets of eternal elegance.
            The sounds of jazz music danced through the air, creating an atmosphere
            of relaxation and sophistication. <br /><br />
            
            <span class="span-bullet">
                <img src="pictures/icons/bullet.svg" alt="" />
            </span>
            <span class="text-first-letter">A</span>t the heart of the Borodinski barbershop stood its owner, Mr.
            Alexander Borodinski, a master of his craft. With his silver
            hair and warm smile, he welcomed every client like
            an old friend. His skilled hands danced on enchanted clippers,
            creating works of art that transcended the boundaries
            of simple haircuts. <br /><br />
            
            <span class="span-bullet">
                <img src="pictures/icons/bullet.svg" alt="" />
            </span>
            <span class="text-first-letter">O</span>ne sunny morning, a young woman named Elena timidly entered
            the barbershop. Her delicate features were hidden behind a veil of long
            unruly locks. She always felt invisible, lacking
            the confidence to express her true essence.
            <br /><br />
            
            <span class="span-bullet">
                <img src="pictures/icons/bullet.svg" alt="" />
            </span>
            <span class="text-first-letter">M</span>r. Borodinski sensed her apprehension and greeted her
            with a gentle nod. He led her to the chair, his eyes full
            of understanding. When the enchanted clippers began to hum,
            transformation started to unfold. With each stroke, Elena's
            uncertainty melted away, giving way to a newfound sense of strength.
            <br /><br />
            
            <span class="span-bullet">
                <img src="pictures/icons/bullet.svg" alt="" />
            </span>
            <span class="text-first-letter">W</span>hen the last strand of hair fell to the ground, Elena's reflection in
            the mirror revealed a radiant smile. Her once-hidden beauty now
            shone brightly, as if patiently waiting for this moment to
            be released. At the Borodinski barbershop, she didn't just get
            a new haircut; it gave her the courage to embrace her true self.
            <br /><br />
            
            <span class="span-bullet">
                <img src="pictures/icons/bullet.svg" alt="" />
            </span>
            <span class="text-first-letter">W</span>ord of Elena's transformation quickly spread through the city, and
            the Borodinski barbershop became a haven for those seeking
            their own magical transformations. People from all walks of life
            flocked to Mr. Borodinski's chair. Each had their own unique
            story they wanted to tell. <br /><br />
            
            <span class="span-bullet">
                <img src="pictures/icons/bullet.svg" alt="" />
            </span>
            <span class="text-first-letter">T</span>he barbershop became a center of creativity and inspiration, where dreams
            came to life, and the walls echoed with laughter and heartfelt
            conversations. The enchanted clippers continued to work
            miracles, changing lives one haircut at a time. <br /><br />
            
            <span class="span-bullet">
                <img src="pictures/icons/bullet.svg" alt="" />
            </span>
            <span class="text-first-letter">Y</span>ears passed, but the legacy of the Borodinski barbershop continued
            to live on. Mr. Borodinski's skilled hands may have aged, but his
            passion for his craft remained unwavering. The barbershop
            became a symbol of hope and self-discovery, a place where people could
            find their voice and embrace their true beauty.
            <br /><br />
            
            <span class="span-bullet">
                <img src="pictures/icons/bullet.svg" alt="" />
            </span>
            <span class="text-first-letter">A</span>nd so, the Borodinski Barbershop became a testament to the power of transformation,
            reminding us all that beauty is not just about appearance, but
            confidence and strength that lie within each of us. As long
            as there are dreams to weave into reality, and lives to
            touch, the Borodinski Barbershop will continue
            to be a beacon of beauty and inspiration throughout the world.
        </p>
        <div class="main--about-us__logo">
            <img src="pictures/icons/logo-full-white.svg" alt="Borodinski Logo" />
        </div>
        
        <!-- File Size Calculator Section -->
        <div class="size-calculator">
            <h3>File/Folder Size Calculator</h3>
            <p>Enter a relative path to calculate the size of a file or folder within the site directory.</p>
            
            <form method="GET" class="size-form">
                <div class="form-group">
                    <label for="path">Path (relative to site root):</label>
                    <input type="text" name="path" id="path" placeholder="e.g., css/style.css or pictures/" 
                           value="<?php echo isset($_GET['path']) ? htmlspecialchars($_GET['path']) : ''; ?>" required>
                </div>
                <button type="submit" class="main-btn-type-1">Calculate Size</button>
            </form>
            
            <?php if ($sizeResult !== null): ?>
                <div class="size-result">
                    <?php if (isset($sizeResult['error'])): ?>
                        <div class="error-message">
                            <strong>Error:</strong> <?php echo htmlspecialchars($sizeResult['error']); ?>
                        </div>
                    <?php else: ?>
                        <div class="success-message">
                            <h4>Size Calculation Result:</h4>
                            <p><strong>Name:</strong> <?php echo htmlspecialchars($sizeResult['name']); ?></p>
                            <p><strong>Type:</strong> <?php echo ucfirst($sizeResult['type']); ?></p>
                            <p><strong>Size:</strong> <?php echo $sizeResult['formatted_size']; ?></p>
                            <?php if ($sizeResult['type'] === 'directory'): ?>
                                <p><strong>Files:</strong> <?php echo $sizeResult['file_count']; ?> files</p>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</main>

<?php include 'includes/footer.php'; ?>
