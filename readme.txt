Hallo nama saya Yozan Adiprada, berikut sedikit mengenai diri saya dan deskripsi RestAPI project yang saya kerjakan menggunakan PHP.

Nama : Yozan Adiprada
Email : adipradayozan@gmail.com
Contact : 62821 5395 8016

Skema untuk akses user :
1// User melakukan request token untuk mendapatkan authentikasi sebelum mengakses endpoint kirim email.
2// setelah mendapatkan akses token dengan batas akses 5 menit, user menyematkan token yg di dapat pada header endpoint kirim email.
3// user juga dapat melakukan cek token dengan cara menyematkan token yang didapat pada header endpoint cek token.
4// dokumentasi endpoint dapat di lihat pada file ApiTest.rest yang ada di dalam directory project.

Mengenai RestAPI kirim email yang saya buat :
1// Dibuat menggunakan PHP tanpa framework.
2// Menggunakan composer.
3// Menggunakan library "phpmailer/phpmailer": "^6.9" dan "google/apiclient": "^2.15"
4// Saya menggunakan SMTP relay brevo untuk proses kirim email.
4// Akses API authenticated dan Authorized menggunakan Google Oauth2.
5// Proses kirim email dari sisi client menggunakan queue proses.
6// Queue proses / cron dapat di request berkala menurut waktu yang di tentukan jika berjalan pada hosting server atau vps.
7// Cron job / queue proses dapat dijalankan manual pada windows dengan perintah beikut :
    "C:\xampp\php\php.exe" C:\xampp\htdocs\codechalenge\process_queue.php

Code chalenge source :
https://docs.google.com/document/d/15gX1vDrvcqcgIqBstaVL3yDIBi3VwneC4qnpPjmu1bg/edit

Repository project (code chalenge) :
https://github.com/adipraday/1f323b5f67c3a8f9d88b72e4c36cc521

Portofolio pekerjaan saya :
https://github.com/adipraday

Penghubung / PIC :
Nama : Gabriella (Gaby)
Contact : +62 812-9780-2896