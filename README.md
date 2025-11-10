# Haekkhon PHP Framework

เล็กกกกกๆๆๆ เรียบง่าย เหมาะสำหรับการเรียนรู้และโปรเจคขนาดเล็กกกกกๆๆๆ — ไม่มี dependency เยอะ ไม่มี magic มากวนใจ

Haekkhon ถูกออกแบบมาเพื่อให้คุณเข้าใจหลักการของ PHP framework พื้นฐาน: routing แบบง่าย, entry point เดียว และโค้ดที่อ่านง่ายเพื่อให้เริ่มต้นได้เร็ว

## คุณสมบัติหลัก

- Routing แบบพื้นฐาน — กำหนด URI -> handler (closure หรือฟังก์ชัน)
- Single entry point — คำขอทั้งหมดผ่าน `public/index.php`
- ติดตั้งง่าย และรองรับการวางใน Subdirectory

## ข้อกำหนด (Requirements)

- PHP 7.4 หรือใหม่กว่า
- Web server ที่รองรับ URL Rewriting (เช่น Apache + mod_rewrite)

## โครงสร้างตัวอย่าง

วางไฟล์ในโฟลเดอร์ของเว็บเซิร์ฟเวอร์ให้มีโครงสร้างแบบนี้:

```
haekkhon-framework/
├── public/
│   ├── index.php
│   └── .htaccess
└── haekkhon.php
```

หมายเหตุ: ให้ตั้งค่า DocumentRoot ชี้ไปที่โฟลเดอร์ `public/` เพื่อความปลอดภัยและความเรียบร้อยของ URL

## ติดตั้งและรันอย่างเร็ววววววว.....

1. คัดลอกหรือโคลนโปรเจคไปไว้ในโฟลเดอร์ของเว็บเซิร์ฟเวอร์ ไม่ต้องใช้ Composer ให้เสียเวลา
2. ตั้งค่า DocumentRoot ให้ชี้ไปที่ `public/`
3. เปิดเบราว์เซอร์ที่ URL ของโปรเจค เช่น `http://localhost/haekkhon-framework/public/`

## ตัวอย่างการใช้งาน (Routing)

เปิดไฟล์ `public/index.php` แล้วคุณจะเห็นตัวอย่างการกำหนดเส้นทางดังนี้:

```php
<?php
require_once '../haekkhon.php';

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

// ตัวอย่างเส้นทางเพิ่มเติม
route('/about', function() {
    echo "<h1>About My World!</h1>";
});

// เริ่ม router
dispatch();
```

ฟังก์ชันสำคัญ:

- `route($uri, $handler)` — ลงทะเบียนเส้นทางใหม่
- `dispatch()` — ประมวลผลคำขอและเรียก handler ที่ตรงกับ URI

## License

ใช้ MIT License ครับ อยากจะเอาไปทำอะไรก็แล้วแต่สะดวกเลย