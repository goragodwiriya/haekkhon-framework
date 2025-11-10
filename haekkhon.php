<?php // haekkhon.php

/**
 * Haekkhon PHP Framework
 * Framework บางๆ ที่ทำงานด้วยฟังก์ชัน
 */

// ตัวแปรเก็บเส้นทางทั้งหมดที่ผู้ใช้กำหนด
$routes = [];

/**
 * ฟังก์ชันสำหรับกำหนดเส้นทาง (Route)
 *
 * @param string $uri       URL ที่ต้องการ (เช่น '/', '/hello')
 * @param callable $handler ฟังก์ชันที่จะทำงานเมื่อเรียก URL นี้
 */
function route(string $uri, callable $handler)
{
    global $routes;
    // ตัด / ท้ายสุดออกเพื่อความสม่ำเสมอ เช่น /hello/ จะกลายเป็น /hello
    $uri = $uri !== '/' ? rtrim($uri, '/') : $uri;
    $routes[$uri] = $handler;
}

/**
 * ฟังก์ชันสำหรับเริ่มต้นการทำงานของ Router
 * จะตรวจสอบ URL ปัจจุบันและเรียกฟังก์ชันที่ตรงกัน
 */
function dispatch()
{
    global $routes;

    // --- เริ่มตรวจสอบ Base Path ---
    // หา Base Path จากตำแหน่งของไฟล์ index.php
    // dirname($_SERVER['PHP_SELF']) จะได้ /haekkhong-framework/public
    $basePath = rtrim(dirname($_SERVER['PHP_SELF']), '/');

    // ดึง URI ปัจจุบันแบบเต็มๆ
    $fullUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

    // ตัด Base Path ออกจาก Full URI
    $uri = str_replace($basePath, '', $fullUri);
    // --- สิ้นสุดการตรวจสอบ Base Path ---

    // ตัด / ท้ายสุดออกเพื่อความสม่ำเสมอ
    $uri = rtrim($uri, '/');

    // ถ้า $uri ว่างเปล่า (เช่นเข้าโฟลเดอร์หลัก) ให้เป็น /
    if ($uri === '') {
        $uri = '/';
    }

    // ตรวจสอบว่ามีเส้นทางนี้ใน $routes หรือไม่ (รองรับ exact match)
    if (array_key_exists($uri, $routes)) {
        // ถ้ามี ให้เรียกฟังก์ชันที่กำหนดไว้
        $handler = $routes[$uri];
        call_user_func($handler);
        return;
    }

    // ถ้าไม่มี exact match ให้ลองแม็ทช์แบบมีพาราม (เช่น /api/books/{id})
    foreach ($routes as $routePattern => $handler) {
        // สนใจเฉพาะ pattern ที่มีวงเล็บปีกกา
        if (strpos($routePattern, '{') === false) {
            continue;
        }

        // แปลง pattern เช่น /api/books/{id} -> regex แบบมี named capture
        // พร้อมดึงลำดับชื่อพาราม เพื่อส่งเป็น positional args ให้ handler
        preg_match_all('/\{([a-zA-Z_][a-zA-Z0-9_]*)\}/', $routePattern, $paramMatches);
        $paramNames = $paramMatches[1] ?? [];

        $regex = preg_replace_callback('/\{([a-zA-Z_][a-zA-Z0-9_]*)\}/', function ($m) {
            return '(?P<'.$m[1].'>[^/]+)';
        }, $routePattern);

        // ทำเป็น regex เต็มรูปแบบ
        $regex = '#^'.$regex.'$#u';

        if (preg_match($regex, $uri, $matches)) {
            // ดึงค่า named captures ออกมาเป็น positional array ตามลำดับที่ปรากฏใน pattern
            $paramsList = [];
            foreach ($paramNames as $pname) {
                if (isset($matches[$pname])) {
                    $paramsList[] = $matches[$pname];
                }
            }

            // ถ้ามีพาราม ให้เรียก handler พร้อมพาราม ถ้าไม่มี ให้เรียกไม่ใส่พาราม (compatibility)
            if (count($paramsList) > 0) {
                call_user_func_array($handler, $paramsList);
            } else {
                call_user_func($handler);
            }
            return;
        }
    }

    // ถ้าไม่มี: คืนค่า 404 ...
    // ถ้าไม่มี: คืนค่า 404 แบบ JSON ถ้าเป็น API / AJAX หรือเมื่อ Accept: application/json
    $accept = $_SERVER['HTTP_ACCEPT'] ?? '';
    $isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    $isApiPath = strpos($uri, '/api') === 0;

    http_response_code(404);

    if ($isApiPath || stripos($accept, 'application/json') !== false || $isAjax) {
        header('Content-Type: application/json; charset=utf-8');
        $payload = [
            'success' => false,
            'error' => 'Not Found',
            'message' => 'The requested endpoint was not found.'
        ];
        // Include debug info only in development
        if (getenv('APP_ENV') === 'development') {
            $payload['debug'] = ['requested_uri' => $uri];
        }
        echo json_encode($payload, JSON_UNESCAPED_UNICODE);
    } else {
        // ถ้าเป็น request ธรรมดา ให้แสดง 404 HTML
        echo "<h1>404 Not Found</h1>";
        echo "<p>Sorry, the page you are looking for does not exist.</p>";
        if (getenv('APP_ENV') === 'development') {
            echo "<small>Debug: Requested URI was '{$uri}'</small>";
        }
    }
}
