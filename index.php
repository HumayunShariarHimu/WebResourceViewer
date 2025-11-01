<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <title>WebResourceViewer</title>
    <link rel="stylesheet" href="your-stylesheet.css">


    <link rel="icon" href="your-favicon.ico" type="image/x-icon">
<meta name="description" content="Explore and analyze web resources with ease using the Web Resource Viewer. View HTML source code, external CSS, external JavaScript, and more. Copy and analyze web content effortlessly.">
 
    
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #fff;
            margin: 0;
            padding: 0;
        }

        #banner {
            background-color: #007bff;
            color: #fff;
            text-align: center;
            padding: 10px 0;
        }

        #banner h1 {
            margin: 0;
            padding: 10px;
        }

        #container {
            width: 80%;
            margin: 20px auto;
            background-color: #fff;
            border: 1px solid #ccc;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 4px;
        }

        form {
            text-align: center;
            margin-bottom: 20px;
        }

        #urlInput {
            width: 70%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        button {
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            border: none;
            cursor: pointer;
            margin-right: 10px;
            border-radius: 4px;
        }

        button:hover {
            background-color: #fff;
        }

        #display {
            width: 100%;
            border: 1px solid #ccc;
            overflow: auto;
            padding: 10px;
            min-height: 300px;
            border-radius: 4px;
        }

        select {
            padding: 10px;
        }

        pre {
            white-space: pre-wrap;
        }

        .copy-button {
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            border: none;
            cursor: pointer;
            margin-top: 10px;
            border-radius: 4px;
        }

        .copy-button:hover {
            background-color: #f0f0f0;
        }








   
        @media (max-width: 768px) {
            #container {
                width: 90%;
            }
            #urlInput {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div id="banner">
        <h1>Web Resource Viewer</h1>
    </div>
    <div id="container">
        <form method="post">
            <label for="urlInput">Enter Website URL:</label>
            <input type="url" id="urlInput" name="url" placeholder="https://example.com" required>
            <button type="submit" name="viewSourceButton">View Source</button>
            <select name="sourceType">
                <option value="html">HTML Source</option>
                <option value="external_css">External CSS</option>
                <option value="external_js">External JavaScript</option>
                <option value="js_code">JavaScript Code</option>
                <option value="css">CSS Source</option>
                       <option value="css">Made By Humayun Shariar Himu</option>
            </select>
        </form>

    </div>
        <div id="display">
           <?php
            if (isset($_POST['url'])) {
                $url = $_POST['url'];

       
                function getExternalCSS($cssUrl) {
                    return file_get_contents($cssUrl);
                }

                
                function getExternalJavaScript($jsUrl) {
                    $jsContent = @file_get_contents($jsUrl);
                    if ($jsContent === false) {
                        return 'Error: Unable to fetch JavaScript file.';
                    }
                    return $jsContent;
                }


                function getExternalResources($html, $resourceType, $baseUrl) {
                    $matches = array();
                    if ($resourceType === 'external_css') {
                        preg_match_all('/<link[^>]+rel="stylesheet"[^>]+href="([^"]+)"/i', $html, $matches);
                    } elseif ($resourceType === 'external_js') {
                        preg_match_all('/<script[^>]+src="([^"]+)"/i', $html, $matches);
                    } elseif ($resourceType === 'js_code') {
                        preg_match_all('/<script[^>]*>(.*?)<\/script>/is', $html, $matches);
                    } elseif ($resourceType === 'css') {
                        preg_match_all('/<style[^>]*>(.*?)<\/style>/is', $html, $matches);
                    }

                    $resources = $matches[1];
                    $fullResources = array();
                    foreach ($resources as $resource) {
                  
                        if ($resource && strpos($resource, 'http') !== 0) {
                    
                            $resource = rtrim($baseUrl, '/') . '/' . ltrim($resource, '/');
                        }
                        $fullResources[] = $resource;
                    }
                    return $fullResources;
                }

                
                if (!preg_match("~^(?:f|ht)tps?://~i", $url)) {
                    $url = "http://" . $url;
                }


                $html = file_get_contents($url);

           
                $sourceType = $_POST['sourceType'];
                if ($sourceType === 'html') {
                 
                    echo '<pre>' . htmlspecialchars($html) . '</pre>';
                } elseif ($sourceType === 'external_css') {
                    
                    $baseUrl = dirname($url);
                    $externalCSSLinks = getExternalResources($html, $sourceType, $baseUrl);
                    echo '<h2>External CSS Links:</h2>';
                    foreach ($externalCSSLinks as $link) {
                        echo '<h3>Source URL: ' . htmlspecialchars($link) . '</h3>';
                        $cssContent = getExternalCSS($link);
                        echo '<pre>' . htmlspecialchars($cssContent) . '</pre>';
                    }
                } elseif ($sourceType === 'external_js') {
             
                    $baseUrl = dirname($url);
                    $externalJSLinks = getExternalResources($html, $sourceType, $baseUrl);
                    echo '<h2>External JavaScript Links:</h2>';
                    foreach ($externalJSLinks as $link) {
                        echo '<h3>Source URL: ' . htmlspecialchars($link) . '</h3>';
                        $jsContent = getExternalJavaScript($link);
                        echo '<pre>' . htmlspecialchars($jsContent) . '</pre>';
                    }
                } elseif ($sourceType === 'js_code') {

                    $jsCode = getExternalResources($html, $sourceType, $url);
                    echo '<h2>JavaScript Code:</h2>';
                    foreach ($jsCode as $code) {
                        echo '<pre>' . htmlspecialchars($code) . '</pre>';
                    }
                } elseif ($sourceType === 'css') {
                  
                    $cssCode = getExternalResources($html, $sourceType, $url);
                    echo '<h2>CSS Source:</h2>';
                    foreach ($cssCode as $code) {
                        echo '<pre>' . htmlspecialchars($code) . '</pre>';
                    }
                }
            }
            ?>
        </div>
        <button class="copy-button" onclick="copyToClipboard()">Copy All</button>
    </div>

    <script>
        function copyToClipboard() {
            const display = document.getElementById('display');
            const range = document.createRange();
            range.selectNode(display);
            const selection = window.getSelection();
            selection.removeAllRanges();
            selection.addRange(range);
            document.execCommand('copy');
            alert('Code copied to clipboard');
        }
        

    </script>
    
</body>
</html>




