


=====ขั้นตอนการติดตั้ง ภาษาไทย=====

ขั้นตอนการติดตั้ง แบ่งออกเป็น 2 แบบ ให้เลือกทำเพียง 1 แบบ

A. ติดตั้งแบบง่าย

  1. แตกไฟล์ออกจาก zip หรือ 7z ลงในไดเร็คทอรี่ของเว็บไซต์

  2. เปิดที่อยู่เว็บที่ต้องการติดตั้ง แล้วตามด้วย /install เช่น http://localhost/install

  3. อ่านคำแนะนำบนหน้าจอ รวมถึงการกำหนดการอนุญาตเขียนบนไดเร็คทอรี่และไฟล์ต่างๆ และทำตามทีละขั้นทีละตอนจนเสร็จ

  4. ลบไดเร็คทอรี่ /install ทิ้งไป

  5. เสร็จขั้นตอนแบบง่าย

B. ติดตั้งด้วยตัวเอง

  1. แตกไฟล์ออกจาก zip หรือ 7z ลงในไดเร็คทอรี่ของเว็บไซต์

  2. กำหนดการอนุญาตเขียนและลบบนไดเร็คทอรี่และไฟล์ต่างๆต่อไปนี้
    - application/cache
    - application/logs
    - modules (และย่อยๆลงไปทั้งหมด)
    - public/upload (และย่อยๆลงไปทั้งหมด)
    - public/themes

  3. นำเข้าไฟล์ install/agni-empty-install.sql ไปในฐานข้อมูล ภายในชื่อฐานข้อมูลที่คุณกำหนดเอง

  4. ตั้งค่าฐานข้อมูลในไฟล์ application/config/database.php โดยต้องกำหนดค่าต่างๆเหล่านี้ให้ถูกต้อง
    - hostname, username, password, database

  5. ลบไดเร็คทอรี่ /install ทิ้งไป

  6. บันทึกเข้าหน้าผู้ดูแล โดยเข้าไปที่ /site-admin และใช้ชื่อผู้ใช้ admin รหัสผ่าน pass



=====Installation instruction English=====

The installation is divided into two types to choose just one.

A. Easy install.

  1. Extract files from the zip or 7z into the directory of the website.

  2. Browse to the web address and followed by /install. eg http://localhost/install.

  3. Read the instructions on the screen including change permission to writable on the directories and files and follow the step by step until finished.

  4. Delete the directory /install

  5. Finished.

B. Manual install.

  1. Extract files from the zip or 7z into the directory of the website.

  2. Set write and delete permission to these directories and files. (chmod 777 on Linux, read/write/delete on Windows).
    - application/cache.
    - application/logs.
    - modules (all sub directories and files)
    - public/upload (all sub directories and files)
    - public/themes.

  3. Import install/agni-empty-install.sql in to database.

  4. Change configuration in application/config/database.php and these things must be configured correctly.
    - hostname, username, password, database.

  5. Delete the directory /install

  6. I go to the site admin page at /site-admin and use this username and password (admin/pass).
