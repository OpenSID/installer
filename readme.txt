# OpenSID Installer

![welcome](https://user-images.githubusercontent.com/57283157/80867836-8028fa00-8cc0-11ea-986c-142f153f02da.PNG)

## Persiapan
- Siapkan OpenSID (https://github.com/OpenSID/OpenSID)
- Siapkan OpenSID-Installer (https://github.com/afa28/OpenSID-Installer)

## Database
Database yang digunakan adalah contoh_data_awal_20200501.sql yang berupa contoh data awal yg diikutkan pada setiap rilis. Adapun perubahan yang dilakukan adalah perubahan struktur database yang diambil dari hasil export phpmyadmin (agar menyesuaikan dengan instller).

Menggunakan database lain :
1. Siapkan database yang akan digunakan (database merupakan hasil export dari phpmyadmin).
2. Ganti nama file database menjadi "opensid.sql".
3. Salin file database ke dalam folder "intall/sql" (Jika muncul peringatan timpa file, silahkan setujui).

## Cara Penggunaan
1. Extract OpenSID dan OpenSID-Intaller pada folder (Jika muncul peringatan timpa file, silahkan setujui):
   - localhost : folder project (../htdoc/nama_project)
   - hosting : public_html / wwww
2. Buat database baru.
3. Buka website anda (contoh http://localhost/opensid/).
4. Ikuti arahan yang ada di form instalasi, lakukan input data jika dibutuhkan.
5. Setelah proses instalasi selesai, anda akan di arahkan ke halaman login admin.


## Catatan
- Gunakan database yg baru/kosong (tidak ada data maupun tabel).
- Centang 'kosongkan dataawal' pada proses import jika ingin menggunakan data kosong.
- Proses import database akan memakan waktu beberapa menit tergantung database dan hsoting yg anda digunakan.
- Semua proses yg dibutuhkan opensid (sperti pembuatan folder desa) sudah include pada installer.
- Setelah proses instalasi selesai semua file.folder yang berhubungan dengan instalasi akan otomatis terhapus.

## Epilog
Jika repository ini dirasa bermanfaat, mohon kesediaannya memberi bintang ⭐
