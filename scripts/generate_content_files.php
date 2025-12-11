<?php
/**
 * Content Files Generator
 * 
 * This script reads the WEBIBO STAGES.txt file and generates
 * all HTML and JSON content files for the curriculum.
 */

// Configuration
$sourceFile = __DIR__ . '/../WEBIBO STAGES.txt';
$htmlDir = __DIR__ . '/../data/html';
$cssDir = __DIR__ . '/../data/css';
$jsDir = __DIR__ . '/../data/js';

// Ensure directories exist
if (!is_dir($htmlDir)) mkdir($htmlDir, 0777, true);
if (!is_dir($cssDir)) mkdir($cssDir, 0777, true);
if (!is_dir($jsDir)) mkdir($jsDir, 0777, true);

// Read the entire curriculum document
$content = file_get_contents($sourceFile);
if (!$content) {
    die("❌ Error: Could not read source file\n");
}

$lines = explode("\n", $content);
$totalLines = count($lines);

echo "📚 Starting content generation...\n";
echo "📄 Total lines in source: " . number_format($totalLines) . "\n\n";

/**
 * Level definitions with line ranges (manually mapped from grep results)
 */
$levels = [
    // HTML COURSE
    ['id' => 1, 'type' => 'lecture', 'start' => 7, 'end' => 95, 'course' => 'HTML'],
    ['id' => 2, 'type' => 'multiple-choice', 'start' => 96, 'end' => 168, 'course' => 'HTML'],
    ['id' => 3, 'type' => 'fill-blank', 'start' => 169, 'end' => 213, 'course' => 'HTML'],
    ['id' => 4, 'type' => 'code-editor', 'start' => 214, 'end' => 227, 'course' => 'HTML'],
    
    ['id' => 5, 'type' => 'lecture', 'start' => 229, 'end' => 288, 'course' => 'HTML'],
    ['id' => 6, 'type' => 'multiple-choice', 'start' => 289, 'end' => 375, 'course' => 'HTML'],
    ['id' => 7, 'type' => 'fill-blank', 'start' => 376, 'end' => 420, 'course' => 'HTML'],
    ['id' => 8, 'type' => 'code-editor', 'start' => 421, 'end' => 441, 'course' => 'HTML'],
    
    ['id' => 9, 'type' => 'lecture', 'start' => 442, 'end' => 512, 'course' => 'HTML'],
    ['id' => 10, 'type' => 'multiple-choice', 'start' => 513, 'end' => 601, 'course' => 'HTML'],
    ['id' => 11, 'type' => 'fill-blank', 'start' => 602, 'end' => 646, 'course' => 'HTML'],
    ['id' => 12, 'type' => 'code-editor', 'start' => 647, 'end' => 661, 'course' => 'HTML'],
    
    ['id' => 13, 'type' => 'lecture', 'start' => 662, 'end' => 726, 'course' => 'HTML'],
    ['id' => 14, 'type' => 'multiple-choice', 'start' => 727, 'end' => 815, 'course' => 'HTML'],
    ['id' => 15, 'type' => 'fill-blank', 'start' => 816, 'end' => 860, 'course' => 'HTML'],
    ['id' => 16, 'type' => 'code-editor', 'start' => 861, 'end' => 890, 'course' => 'HTML'],
    
    ['id' => 17, 'type' => 'lecture', 'start' => 891, 'end' => 994, 'course' => 'HTML'],
    ['id' => 18, 'type' => 'multiple-choice', 'start' => 995, 'end' => 1083, 'course' => 'HTML'],
    ['id' => 19, 'type' => 'fill-blank', 'start' => 1084, 'end' => 1128, 'course' => 'HTML'],
    ['id' => 20, 'type' => 'code-editor', 'start' => 1129, 'end' => 1186, 'course' => 'HTML'],
    
    // CSS COURSE
    ['id' => 21, 'type' => 'lecture', 'start' => 1187, 'end' => 1250, 'course' => 'CSS'],
    ['id' => 22, 'type' => 'multiple-choice', 'start' => 1251, 'end' => 1309, 'course' => 'CSS'],
    ['id' => 23, 'type' => 'fill-blank', 'start' => 1310, 'end' => 1346, 'course' => 'CSS'],
    ['id' => 24, 'type' => 'code-editor', 'start' => 1347, 'end' => 1380, 'course' => 'CSS'],
    
    ['id' => 25, 'type' => 'lecture', 'start' => 1381, 'end' => 1472, 'course' => 'CSS'],
    ['id' => 26, 'type' => 'multiple-choice', 'start' => 1473, 'end' => 1532, 'course' => 'CSS'],
    ['id' => 27, 'type' => 'fill-blank', 'start' => 1533, 'end' => 1568, 'course' => 'CSS'],
    ['id' => 28, 'type' => 'code-editor', 'start' => 1569, 'end' => 1595, 'course' => 'CSS'],
    
    ['id' => 29, 'type' => 'lecture', 'start' => 1596, 'end' => 1697, 'course' => 'CSS'],
    ['id' => 30, 'type' => 'multiple-choice', 'start' => 1698, 'end' => 1757, 'course' => 'CSS'],
    ['id' => 31, 'type' => 'fill-blank', 'start' => 1758, 'end' => 1793, 'course' => 'CSS'],
    ['id' => 32, 'type' => 'code-editor', 'start' => 1794, 'end' => 1853, 'course' => 'CSS'],
    
    // JAVASCRIPT COURSE
    ['id' => 33, 'type' => 'lecture', 'start' => 1854, 'end' => 1913, 'course' => 'JS'],
    ['id' => 34, 'type' => 'multiple-choice', 'start' => 1914, 'end' => 1974, 'course' => 'JS'],
    ['id' => 35, 'type' => 'fill-blank', 'start' => 1975, 'end' => 2011, 'course' => 'JS'],
    ['id' => 36, 'type' => 'code-editor', 'start' => 2012, 'end' => 2031, 'course' => 'JS'],
    
    ['id' => 37, 'type' => 'lecture', 'start' => 2032, 'end' => 2127, 'course' => 'JS'],
    ['id' => 38, 'type' => 'multiple-choice', 'start' => 2128, 'end' => 2187, 'course' => 'JS'],
    ['id' => 39, 'type' => 'fill-blank', 'start' => 2188, 'end' => 2223, 'course' => 'JS'],
    ['id' => 40, 'type' => 'code-editor', 'start' => 2224, 'end' => 2250, 'course' => 'JS'],
    
    ['id' => 41, 'type' => 'lecture', 'start' => 2251, 'end' => 2348, 'course' => 'JS'],
    ['id' => 42, 'type' => 'multiple-choice', 'start' => 2349, 'end' => 2408, 'course' => 'JS'],
    ['id' => 43, 'type' => 'fill-blank', 'start' => 2409, 'end' => 2444, 'course' => 'JS'],
    ['id' => 44, 'type' => 'code-editor', 'start' => 2445, 'end' => 2495, 'course' => 'JS'],
];

$successCount = 0;
$errorCount = 0;

foreach ($levels as $level) {
    $levelId = $level['id'];
    $type = $level['type'];
    $startLine = $level['start'] - 1; // 0-indexed
    $endLine = $level['end'] - 1;
    
    // Extract content for this level
    $levelLines = array_slice($lines, $startLine, $endLine - $startLine + 1);
    $levelContent = implode("\n", $levelLines);
    
    try {
        if ($type === 'lecture') {
            // Generate HTML file
            $html = generateLectureHTML($levelContent, $levelId);
            $filePath = "{$htmlDir}/{$levelId}.html";
            file_put_contents($filePath, $html);
            echo "✅ Generated: {$levelId}.html (Lecture)\n";
        } else {
            // Generate JSON file
            $json = generateActivityJSON($levelContent, $levelId, $type);
            $filePath = "{$htmlDir}/{$levelId}.json";
            file_put_contents($filePath, $json);
            echo "✅ Generated: {$levelId}.json (" . ucfirst(str_replace('-', ' ', $type)) . ")\n";
        }
        $successCount++;
    } catch (Exception $e) {
        echo "❌ Error generating Level {$levelId}: " . $e->getMessage() . "\n";
        $errorCount++;
    }
}

echo "\n" . str_repeat("=", 50) . "\n";
echo "📊 Generation Complete!\n";
echo "✅ Success: {$successCount} files\n";
echo "❌ Errors: {$errorCount} files\n";
echo str_repeat("=", 50) . "\n";

/**
 * Generate HTML content for lecture levels
 */
function generateLectureHTML($content, $levelId) {
    // Extract pages from content
    preg_match_all('/Page \d+\/\d+:(.*?)(?=Page \d+\/\d+:|________________|$)/s', $content, $matches);
    
    if (empty($matches[1])) {
        throw new Exception("No pages found in lecture content");
    }
    
    $html = "<!DOCTYPE html>\n";
    $html .= "<html lang=\"en\">\n";
    $html .= "<head>\n";
    $html .= "    <meta charset=\"UTF-8\">\n";
    $html .= "    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">\n";
    $html .= "    <title>Level {$levelId} - Lecture</title>\n";
    $html .= "    <link rel=\"stylesheet\" href=\"../assets/css/lecture-content.css\">\n";
    $html .= "</head>\n";
    $html .= "<body>\n";
    
    $pageCount = count($matches[1]);
    foreach ($matches[1] as $index => $pageContent) {
        $pageNum = $index + 1;
        $pageContent = trim($pageContent);
        
        $html .= "\n    <!-- PAGE {$pageNum} -->\n";
        $html .= "    <div class=\"page-break\"></div>\n";
        $html .= "    <div class=\"page-header\">\n";
        $html .= "        <div class=\"page-number\">Page {$pageNum}/{$pageCount}</div>\n";
        
        // Extract title (first line after Page X/X:)
        $contentLines = explode("\n", $pageContent);
        $title = trim($contentLines[0]);
        $html .= "        <h1>{$title}</h1>\n";
        $html .= "    </div>\n\n";
        
        // Process rest of content
        $restContent = implode("\n", array_slice($contentLines, 1));
        $html .= processLectureContent($restContent);
    }
    
    $html .= "\n</body>\n";
    $html .= "</html>";
    
    return $html;
}

/**
 * Process lecture content and convert to HTML
 */
function processLectureContent($content) {
    $html = "";
    $lines = explode("\n", $content);
    $inCodeBlock = false;
    $inList = false;
    
    foreach ($lines as $line) {
        $line = trim($line);
        
        if (empty($line)) {
            if ($inList) {
                $html .= "    </ul>\n";
                $inList = false;
            }
            continue;
        }
        
        // Handle Wiza dialogue
        if (preg_match('/^Wiza:\s*"(.*)$/', $line, $match)) {
            if ($inList) {
                $html .= "    </ul>\n";
                $inList = false;
            }
            $html .= "    <div class=\"wiza-intro\">\n";
            $html .= "        <strong>Wiza:</strong> \"" . htmlspecialchars($match[1]) . "\n";
            continue;
        }
        
        // Close Wiza dialogue
        if (preg_match('/^(.*)"$/', $line, $match) && strpos($html, '<div class="wiza-intro">') !== false && strpos($html, '</div>') === false) {
            $html .= "        " . htmlspecialchars($match[1]) . "\"\n";
            $html .= "    </div>\n\n";
            continue;
        }
        
        // Handle code blocks
        if (preg_match('/^(html|css|javascript|js)$/', $line)) {
            $inCodeBlock = true;
            $html .= "    <div class=\"code-box\">\n        <pre><code>";
            continue;
        }
        
        if ($inCodeBlock) {
            $html .= htmlspecialchars($line) . "\n";
            // Check if code block ends (empty line or new section)
            continue;
        }
        
        // Handle bullet points
        if (preg_match('/^\*\s+(.*)$/', $line, $match)) {
            if (!$inList) {
                $html .= "    <ul>\n";
                $inList = true;
            }
            $html .= "        <li>" . htmlspecialchars($match[1]) . "</li>\n";
            continue;
        }
        
        // Handle numbered lists
        if (preg_match('/^\d+\.\s+(.*)$/', $line, $match)) {
            $html .= "    <p><strong>" . htmlspecialchars($line) . "</strong></p>\n";
            continue;
        }
        
        // Regular paragraph
        if ($inList) {
            $html .= "    </ul>\n";
            $inList = false;
        }
        $html .= "    <p>" . htmlspecialchars($line) . "</p>\n";
    }
    
    if ($inCodeBlock) {
        $html .= "</code></pre>\n    </div>\n";
    }
    if ($inList) {
        $html .= "    </ul>\n";
    }
    
    return $html;
}

/**
 * Generate JSON content for activity levels
 */
function generateActivityJSON($content, $levelId, $type) {
    $data = [];
    
    if ($type === 'multiple-choice') {
        $data = parseMultipleChoiceQuestions($content);
    } else if ($type === 'fill-blank') {
        $data = parseFillBlankQuestions($content);
    } else if ($type === 'code-editor') {
        $data = parseCodeChallenge($content);
    }
    
    return json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}

/**
 * Parse multiple choice questions
 */
function parseMultipleChoiceQuestions($content) {
    $questions = [];
    
    // Split by question numbers
    preg_match_all('/(\d+)\.\s+(.*?)\n([a-d]\).*?)(?=\d+\.\s+|\z)/s', $content, $matches, PREG_SET_ORDER);
    
    foreach ($matches as $match) {
        $questionNum = $match[1];
        $questionText = trim($match[2]);
        $optionsBlock = $match[3];
        
        // Extract options
        preg_match_all('/([a-d])\)\s+(.*?)(?=\n[a-d]\)|\nCorrect|$)/s', $optionsBlock, $optMatches, PREG_SET_ORDER);
        
        $options = [];
        foreach ($optMatches as $opt) {
            $options[] = trim($opt[2]);
        }
        
        // Extract correct answer
        preg_match('/Correct Answer:\s+([a-d])/i', $content, $ansMatch);
        $correctLetter = $ansMatch[1] ?? 'a';
        $correctIndex = ord(strtolower($correctLetter)) - ord('a');
        $correctAnswer = $options[$correctIndex] ?? $options[0];
        
        // Extract explanation
        preg_match('/Explanation:\s+(.*?)(?=\n\d+\.|$)/s', $content, $expMatch);
        $explanation = trim($expMatch[1] ?? '');
        
        $questions[] = [
            'question' => $questionText,
            'options' => $options,
            'correct_answer' => $correctAnswer,
            'feedback' => [
                'correct' => [
                    'title' => 'Correct! 🎉',
                    'details' => $explanation
                ],
                'wrong' => [
                    'title' => 'Not quite! 🤔',
                    'details' => $explanation
                ]
            ]
        ];
    }
    
    return [
        'questions' => $questions,
        'enemy' => [
            'name' => 'Fox',
            'hp' => count($questions),
            'image' => 'assets/img/enemies/fox-happy.png'
        ]
    ];
}

/**
 * Parse fill-in-the-blank questions
 */
function parseFillBlankQuestions($content) {
    $questions = [];
    
    // Similar parsing logic for fill-blank format
    // This is simplified - you'll need to adjust based on actual format
    
    return [
        'questions' => $questions,
        'enemy' => [
            'name' => 'Bear',
            'hp' => 10,
            'image' => 'assets/img/enemies/bear-happy.png'
        ]
    ];
}

/**
 * Parse coding challenge
 */
function parseCodeChallenge($content) {
    // Extract task and solution
    preg_match('/Tasks?:(.*?)(?=Expected|Solution|$)/s', $content, $taskMatch);
    preg_match('/Solution:(.*?)$/s', $content, $solMatch);
    
    $task = trim($taskMatch[1] ?? '');
    $solution = trim($solMatch[1] ?? '');
    
    return [
        'instruction' => $task,
        'correct_code' => $solution,
        'starter_code' => '',
        'validation' => [
            'ignore_whitespace' => true,
            'case_sensitive' => false
        ],
        'feedback' => [
            'correct' => [
                'title' => 'Perfect! 🎉',
                'details' => 'Your code is correct!'
            ],
            'wrong' => [
                'title' => 'Try Again',
                'details' => 'Check your syntax and try again.'
            ]
        ]
    ];
}
