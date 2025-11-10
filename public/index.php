<?php // public/index.php

// นำเข้าไฟล์หลักของ Framework
require_once '../haekkhon.php';

// --- กำหนดเส้นทางต่างๆ ตรงนี้ ---

// เส้นทางหลัก (/)
route('/', function () {
    echo "<h1>ยินดีต้อนรับสู่ Haekkhon PHP Framework!</h1>";
    echo '<p><a href="hello">คลิกเพื่อไปหน้า Hello World</a></p>';
});

// เส้นทาง /hello สำหรับทดสอบ Hello World
route('/hello', function () {
    echo "<h1>Hello World!</h1>";
    echo '<p>นี่คือผลลัพธ์จากเส้นทาง /hello</p>';
    echo '<a href=".">กลับหน้าหลัก</a>';
});

// --- สิ้นสุดการกำหนดเส้นทาง ---

// เรียกใช้ฟังก์ชัน dispatch() เพื่อเริ่มต้นการทำงานของ Router
dispatch();
