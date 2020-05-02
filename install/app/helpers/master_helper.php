<?php
defined('BASEPATH') or exit('No direct script access allowed');

// Hapus fungsi ini jika installer sudah digabungkan dengan opensid
define("VERSION", '20.05');

// Hapus fungsi ini jika installer sudah digabungkan dengan opensid
function xcopy($src, $dest)
{
	foreach (scandir($src) as $file) {
		$srcfile  = rtrim($src, '/') . '/' . $file;
		$destfile = rtrim($dest, '/') . '/' . $file;
		if (!is_readable($srcfile)) {
			continue;
		}
		if ($file != '.' && $file != '..') {
			if (is_dir($srcfile)) {
				if (!file_exists($destfile)) {
					mkdir($destfile);
				}
				xcopy($srcfile, $destfile);
			} else {
				copy($srcfile, $destfile);
			}
		}
	}
}

function delete_folder($path)
{
	if (!file_exists($path)) {
		return false;
	}

	if (is_file($path) || is_link($path)) {
		return unlink($path);
	}

	$stack = array($path);

	while ($entry = array_pop($stack)) {
		if (is_link($entry)) {
			unlink($entry);
			continue;
		}

		if (@rmdir($entry)) {
			continue;
		}

		$stack[] = $entry;
		$dh = opendir($entry);

		while (false !== $child = readdir($dh)) {
			if ($child === '.' || $child === '..') {
				continue;
			}

			$child = $entry . DIRECTORY_SEPARATOR . $child;

			if (is_dir($child) && !is_link($child)) {
				$stack[] = $child;
			} else {
				unlink($child);
			}
		}

		closedir($dh);
	}

	return true;
}

//folder : desa/config/database.php
function cdb($conf)
{
	$db_host = $conf['db_host'];
	$db_user = $conf['db_user'];
	$db_pass = $conf['db_pass'];
	$db_name = $conf['db_name'];
	$db_port = $conf['db_port'];

	$date = date('d-M-Y h:i:s');
	$build_version = VERSION;

	$content = <<<EOS
<?php defined('BASEPATH') OR exit('No direct script access allowed');

/* -------------------------------------------------------------------------
|  Konfigurasi database dalam file ini menggantikan konfigurasi di file asli
|  SID di donjo-app/config/database.php.
|  
|  Letakkan username, password dan database sebetulnya di file ini.
|  File ini JANGAN di-commit ke GIT. TAMBAHKAN di .gitignore
|  -------------------------------------------------------------------------

|  Data Konfigurasi MySQL yang disesuaikan
*/

\$db['default']['hostname'] = '{$db_host}';
\$db['default']['username'] = '{$db_user}';
\$db['default']['password'] = '{$db_pass}';
\$db['default']['database'] = '{$db_name}';

/*
| Untuk setting koneksi database 'Strict Mode'
| Sesuaikan dengan ketentuan hosting
*/ 
\$db['default']['stricton'] = TRUE;
EOS;
	return $content;
}

//folder : desa/config/config.php
function cfile()
{
	$date = date('d-M-Y h:i:s');
	$build_version = VERSION;
	$content = <<<EOS
<?php defined('BASEPATH') OR exit('No direct script access allowed');
// ----------------------------------------------------------------------------
// Konfigurasi aplikasi dalam berkas ini merupakan setting konfigurasi tambahan
// SID. Letakkan setting konfigurasi ini di desa/config/config.php.
// ----------------------------------------------------------------------------

/*
	Uncomment jika situs ini untuk demo. Pada demo, user admin tidak bisa dihapus
	dan username/password tidak bisa diubah
*/
// \$config['demo'] = 'y';

/*
	Password untuk File Manager yg digunakan pada form isian artikel.
	Gunakan password yg sulit ditebak manusia maupun program otomatis.
*/
	\$config['file_manager'] = "GantiKunciDesa";

// Setting ini untuk menentukan user yang dipercaya. User dengan id di setting ini
// dapat membuat artikel berisi video yang aktif ditampilkan di Web.
// Misalnya, ganti dengan id = 1 jika ingin membuat pengguna admin sebagai pengguna terpecaya.
	\$config['user_admin'] = 1;
/*
	Setting untuk tampilkan data Covid-19. Untuk menyembunyikan ganti menjadi nilai 0;
	Untuk menampilkan data provinsi, gunakan setting 'provinsi_covid'.
	Kode provinsi sesuai dengan yg di http://pusatkrisis.kemkes.go.id/daftar-kode-provinsi
*/
	\$config['covid_data'] = 1;
	\$config['provinsi_covid'] = 51; // kode provinsi. Comment baris ini untuk menampilkan data Indonesia
	\$config['covid_desa'] = 1; // Tampilkan status COVID-19 dari data OpenSID desa
EOS;
	return $content;
}

// index.php
function cindex()
{
	$date = date('d-M-Y h:i:s');
	$build_version = VERSION;
	$content = <<<EOS
<?php
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP
 *
 * This content is released under the MIT License (MIT)
 *
 * Copyright (c) 2014 - 2018, British Columbia Institute of Technology
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @package	CodeIgniter
 * @author	EllisLab Dev Team
 * @copyright	Copyright (c) 2008 - 2014, EllisLab, Inc. (https://ellislab.com/)
 * @copyright	Copyright (c) 2014 - 2018, British Columbia Institute of Technology (http://bcit.ca/)
 * @license	http://opensource.org/licenses/MIT	MIT License
 * @link	https://codeigniter.com
 * @since	Version 1.0.0
 * @filesource
 */

/*
 *---------------------------------------------------------------
 * APPLICATION ENVIRONMENT
 *---------------------------------------------------------------
 *
 * You can load different configurations depending on your
 * current environment. Setting the environment also influences
 * things like logging and error reporting.
 *
 * This can be set to anything, but default usage is:
 *
 *     development
 *     testing
 *     production
 *
 * NOTE: If you change these, also change the error_reporting() code below
 */
	define('ENVIRONMENT', isset(\$_SERVER['CI_ENV']) ? \$_SERVER['CI_ENV'] : 'production');

/*
 *---------------------------------------------------------------
 * ERROR REPORTING
 *---------------------------------------------------------------
 *
 * Different environments will require different levels of error reporting.
 * By default development will show errors but testing and live will hide them.
 */
switch (ENVIRONMENT)
{
	case 'development':
		error_reporting(-1);
		ini_set('display_errors', 1);
	break;

	case 'testing':
	case 'production':
		ini_set('display_errors', 0);
		if (version_compare(PHP_VERSION, '5.3', '>='))
		{
			error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT & ~E_USER_NOTICE & ~E_USER_DEPRECATED);
		}
		else
		{
			error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_USER_NOTICE);
		}
	break;

	default:
		header('HTTP/1.1 503 Service Unavailable.', TRUE, 503);
		echo 'The application environment is not set correctly.';
		exit(1); // EXIT_ERROR
}

/*
 *---------------------------------------------------------------
 * SYSTEM DIRECTORY NAME
 *---------------------------------------------------------------
 *
 * This variable must contain the name of your "system" directory.
 * Set the path if it is not in the same directory as this file.
 */
	\$system_path = 'system';

/*
 *---------------------------------------------------------------
 * APPLICATION DIRECTORY NAME
 *---------------------------------------------------------------
 *
 * If you want this front controller to use a different "application"
 * directory than the default one you can set its name here. The directory
 * can also be renamed or relocated anywhere on your server. If you do,
 * use an absolute (full) server path.
 * For more info please see the user guide:
 *
 * https://codeigniter.com/user_guide/general/managing_apps.html
 *
 * NO TRAILING SLASH!
 */
	\$application_folder = 'donjo-app';

/*
 *---------------------------------------------------------------
 * VIEW DIRECTORY NAME
 *---------------------------------------------------------------
 *
 * If you want to move the view directory out of the application
 * directory, set the path to it here. The directory can be renamed
 * and relocated anywhere on your server. If blank, it will default
 * to the standard location inside your application directory.
 * If you do move this, use an absolute (full) server path.
 *
 * NO TRAILING SLASH!
 */
	\$view_folder = '';


/*
 * --------------------------------------------------------------------
 * DEFAULT CONTROLLER
 * --------------------------------------------------------------------
 *
 * Normally you will set your default controller in the routes.php file.
 * You can, however, force a custom routing by hard-coding a
 * specific controller class/function here. For most applications, you
 * WILL NOT set your routing here, but it's an option for those
 * special instances where you might want to override the standard
 * routing in a specific front controller that shares a common CI installation.
 *
 * IMPORTANT: If you set the routing here, NO OTHER controller will be
 * callable. In essence, this preference limits your application to ONE
 * specific controller. Leave the function name blank if you need
 * to call functions dynamically via the URI.
 *
 * Un-comment the \$routing array below to use this feature
 */
	// The directory name, relative to the "controllers" directory.  Leave blank
	// if your controller is not in a sub-directory within the "controllers" one
	// \$routing['directory'] = '';

	// The controller class file name.  Example:  mycontroller
	// \$routing['controller'] = '';

	// The controller function you wish to be called.
	// \$routing['function']	= '';


/*
 * -------------------------------------------------------------------
 *  CUSTOM CONFIG VALUES
 * -------------------------------------------------------------------
 *
 * The \$assign_to_config array below will be passed dynamically to the
 * config class when initialized. This allows you to set custom config
 * items or override any default config values found in the config.php file.
 * This can be handy as it permits you to share one application between
 * multiple front controller files, with each file containing different
 * config values.
 *
 * Un-comment the \$assign_to_config array below to use this feature
 */
	// \$assign_to_config['name_of_config_item'] = 'value of config item';



// --------------------------------------------------------------------
// END OF USER CONFIGURABLE SETTINGS.  DO NOT EDIT BELOW THIS LINE
// --------------------------------------------------------------------

/*
 * ---------------------------------------------------------------
 *  Resolve the system path for increased reliability
 * ---------------------------------------------------------------
 */

	// Set the current directory correctly for CLI requests
	if (defined('STDIN'))
	{
		chdir(dirname(__FILE__));
	}

	if ((\$_temp = realpath(\$system_path)) !== FALSE)
	{
		\$system_path = \$_temp.DIRECTORY_SEPARATOR;
	}
	else
	{
		// Ensure there's a trailing slash
		\$system_path = strtr(
			rtrim(\$system_path, '/\\\'),
			'/\\\',
			DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR
		).DIRECTORY_SEPARATOR;
	}

	// Is the system path correct?
	if ( ! is_dir(\$system_path))
	{
		header('HTTP/1.1 503 Service Unavailable.', TRUE, 503);
		echo 'Your system folder path does not appear to be set correctly. Please open the following file and correct this: '.pathinfo(__FILE__, PATHINFO_BASENAME);
		exit(3); // EXIT_CONFIG
	}

/*
 * -------------------------------------------------------------------
 *  Now that we know the path, set the main path constants
 * -------------------------------------------------------------------
 */
	// The name of THIS file
	define('SELF', pathinfo(__FILE__, PATHINFO_BASENAME));

	// Path to the system directory
	define('BASEPATH', \$system_path);

	// Path to the front controller (this file) directory
	define('FCPATH', dirname(__FILE__).DIRECTORY_SEPARATOR);

	// Name of the "system" directory
	define('SYSDIR', basename(BASEPATH));

	// The path to the "application" directory
	if (is_dir(\$application_folder))
	{
		if ((\$_temp = realpath(\$application_folder)) !== FALSE)
		{
			\$application_folder = \$_temp;
		}
		else
		{
			\$application_folder = strtr(
				rtrim(\$application_folder, '/\\\'),
				'/\\\',
				DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR
			);
		}
	}
	elseif (is_dir(BASEPATH.\$application_folder.DIRECTORY_SEPARATOR))
	{
		\$application_folder = BASEPATH.strtr(
			trim(\$application_folder, '/\\\'),
			'/\\\',
			DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR
		);
	}
	else
	{
		header('HTTP/1.1 503 Service Unavailable.', TRUE, 503);
		echo 'Your application folder path does not appear to be set correctly. Please open the following file and correct this: '.SELF;
		exit(3); // EXIT_CONFIG
	}

	define('APPPATH', \$application_folder.DIRECTORY_SEPARATOR);

	// The path to the "views" directory
	if ( ! isset(\$view_folder[0]) && is_dir(APPPATH.'views'.DIRECTORY_SEPARATOR))
	{
		\$view_folder = APPPATH.'views';
	}
	elseif (is_dir(\$view_folder))
	{
		if ((\$_temp = realpath(\$view_folder)) !== FALSE)
		{
			\$view_folder = \$_temp;
		}
		else
		{
			\$view_folder = strtr(
				rtrim(\$view_folder, '/\\\'),
				'/\\\',
				DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR
			);
		}
	}
	elseif (is_dir(APPPATH.\$view_folder.DIRECTORY_SEPARATOR))
	{
		\$view_folder = APPPATH.strtr(
			trim(\$view_folder, '/\\\'),
			'/\\\',
			DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR
		);
	}
	else
	{
		header('HTTP/1.1 503 Service Unavailable.', TRUE, 503);
		echo 'Your view folder path does not appear to be set correctly. Please open the following file and correct this: '.SELF;
		exit(3); // EXIT_CONFIG
	}

	define('VIEWPATH', \$view_folder.DIRECTORY_SEPARATOR);
/**
 * https://stackoverflow.com/questions/11792268/how-to-set-proper-codeigniter-base-url
 * Define APP_URL Dynamically
 * Write this at the bottom of index.php
 *
 * Automatic base url
 */
	define('APP_URL', (\$_SERVER['SERVER_PORT'] == 443 ? 'https' : 'http') . "://{\$_SERVER['HTTP_HOST']}".str_replace(basename(\$_SERVER['SCRIPT_NAME']),"",\$_SERVER['SCRIPT_NAME']));

/*
/*
 * --------------------------------------------------------------------
 * LOAD THE BOOTSTRAP FILE
 * --------------------------------------------------------------------
 *
 * And away we go...
 */
require_once BASEPATH.'core/CodeIgniter.php';
EOS;
	return $content;
}

// license
function license()
{
	$license = '
# OpenSID
OpenSID (https://github.com/OpenSID/OpenSID) adalah Sistem Informasi Desa (SID) yang sengaja dibuat supaya terbuka dan dapat dikembangkan bersama-sama oleh komunitas peduli SID.

SID diharapkan dapat membantu pemerintah desa dalam beberapa hal berikut:
- kantor desa lebih efisien dan efektif
- pemerintah desa lebih transparan dan akuntabel
- layanan publik lebih baik
- warga mendapat akses lebih baik pada informasi desa

OpenSID diharapkan dapat turut membantu agar ke semua 74ribu+ desa di Indonesia dapat menerapkan sistem informasi untuk memajukan desanya.

Strategi pengembangan OpenSID adalah untuk:
- memudahkan pengguna untuk mendapatkan SID secara bebas, tanpa proses birokrasi
- memudahkan pengguna menyerap rilis SID baru
- memungkinkan pegiat SID untuk membuat kontribusi langsung pada _source code_ aplikasi SID

OpenSID dikelola di Github untuk:
- merekam semua perubahan yg dibuat
- memungkinkan kembali ke revisi sebelumnya, apabila diperlukan
- memudahkan kolaborasi antar pegiat SID dan juga dengan desa dampingan dalam mengembangkan SID
- backup online _source code_ SID yg dapat diaskses setiap saat

# PEDOMAN PENGGUNAAN
Pedoman pemasangan dan penggunaan OpenSID dapat dilihat di wiki OpenSID di https://github.com/opensid/opensid/wiki.

# OpenSID v SID CRI
OpenSID dikembangkan sejak Mei 2016 bermula dari versi SID CRI 3.04 yang diperoleh dari Andi Anwar.

SID CRI terakhir yang telah digabung dengan OpenSID adalah SID 3.10 yang di-share oleh anggota https://www.facebook.com/groups/OpenSID/ pada tanggal 15 Pebruari 2017. OpenSID akan terus menggabung versi baru yang mungkin sewaktu-waktu dirilis oleh CRI.

Untuk melihat perbedaan antara OpenSID dan SID-CRI, silakan klik [Tanya-Jawab OpenSID vs SID-CRI](https://github.com/OpenSID/OpenSID/wiki/Tanya-Jawab-OpenSID-vs-SID-CRI).

# HAK CIPTA, SYARAT, DAN KETENTUAN
Aplikasi Sistem Informasi Desa (SID) dibangun dan dikembangkan pada awalnya oleh COMBINE Resource Institution sejak tahun 2009. Pemegang hak cipta aslinya adalah Combine Resource Institution (http://lumbungkomunitas.net/).

Sistem ini dikelola dengan merujuk pada lisensi GNU General Public License Version 3 (http://www.gnu.org/licenses/gpl.html).

Versi di Github ini dikembangkan oleh OpenSID sejak Mei 2016, dan bebas untuk dimanfaatkan dan dikembangkan oleh semua desa.

# DEMO
Demo aplikasi OpenSID dapat dilihat di http://demo.opensid.my.id. Versi yang terlihat di demo itu adalah rilis terkini. Versi beta dapat diujicoba di https://beta.opensid.my.id sebelum dirilis resmi pada bulan berikutnya.

Modul administrasi OpenSID demo dapat diaskses pada http://demo.opensid.my.id/index.php/siteman. Masukkan Username = admin dan Password = sid304.

# FORUM
Anda dapat bergabung dengan [Forum Pengguna dan Pegiat OpenSID](https://www.facebook.com/groups/opensid) di Facebook atau di [Telegram](http://bit.ly/2DG6Beb). Kedua group ini bersifat informal dan merupakan tempat berbagi informasi dan saling membantu menggunakan dan mengembangkan aplikasi OpenSID.

# KEMBANGKAN BERSAMA
Pengguna dan pegiat SID dapat melaporkan dan mendaftarkan masalah/usulan/permintaan perbaikan atau pengembangan OpenSID di https://github.com/opensid/opensid/issues. Issues ini merupakan daftar tugas bagi pegiat OpenSID untuk mengembangkan OpenSID berdasarkan masukan dari komunitas SID.

Komunitas SID juga bebas, bahkan diajak, untuk turut membuat kontribusi pada panduan OpenSID di https://github.com/OpenSID/OpenSID/wiki, dan pada script OpenSID di (https://github.com/OpenSID/OpenSID).
';
	return $license;
}

//folder : .htaccess
function htaccess()
{
	$content = <<<EOS
#============
# Untuk menghapus index.php dari url OpenSID, ubah nama file ini menjadi .htaccess,
# sehingga misalnya, modul Web bisa dipanggil dengan http://localhost/first.
# Untuk menggunakan fitur ini, pastikan konfigurasi apache di server SID
# mengizinkan penggunaan .htaccess
#============
RewriteEngine on
RewriteBase /
# Apabila menggunakan sub-domain atau sub-folder gunakan bentuk berikut
# RewriteBase /nama-sub-folder/

# Prevent index dirs
RewriteCond $1 
RewriteRule ^(.*)$ index.php/$1 [L,QSA]

# General dirs / files
RewriteCond $1 !^(index\.php|resources|robots\.txt)
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php/$1 [L,QSA]

# Protec Folder Not Index
Options All -Indexes
EOS;
	return $content;
}

function input($val)
{
	$CI =& get_instance();

	return $CI->input->post($val);
}

