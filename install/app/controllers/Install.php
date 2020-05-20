<?php defined('BASEPATH') or exit('No direct script access allowed');

class Install extends MY_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->library('session');
		ini_set('max_execution_time', 0);
		ini_set('memory_limit', '-1');
		date_default_timezone_set('Asia/Jakarta');
	}

	public function index()
	{
		$act = input('act');

		switch ($act)
		{
			case 'cek_server':
				$_SESSION['db_host'] = input('db_host') ? input('db_host') : $_SESSION['db_host'];
				$_SESSION['db_name'] = input('db_name') ? input('db_name') : $_SESSION['db_name'];
				$_SESSION['db_user'] = input('db_user') ? input('db_user') : $_SESSION['db_user'];
				$_SESSION['db_pass'] = input('db_pass') ? input('db_pass') : $_SESSION['db_pass'];

				$dbconfig = $this->_db_config();
				$db_obj = $this->load->database($dbconfig, TRUE);

				if (!$db_obj->conn_id)
				{
					$this->_error();
				}
				else
				{
					$data = array(
						'judul' => 'cek server',
						'tujuan' => 'ke_impor',
						'aksi' => 'lanjutkan'
					);
					$data['server'] = $this->_cek_server($db_obj);
					$this->render_view('cek_server', $data);
				}
				break;

			// Langkah 2 - Pengaturan Database
			case 'ke_basisdata':
				$data = array(
					'judul' => 'pengaturan basisdata',
					'tujuan' => 'cek_server',
					'aksi' => 'Hubungkan'
				);
				$this->render_view('set_database', $data);

				break;

			// Langkah 3 - Pengaturan Impor
			case 'ke_impor':
				$dbconfig = $this->_db_config();
				$db_obj = $this->load->database($dbconfig, TRUE);

				if (!$db_obj->conn_id)
				{
					$this->_error();
				}
				else
				{
					$this->load->database();

					$data = array(
						'judul' => 'impor basisdata',
						'tujuan' => 'proses_impor',
						'aksi' => 'Impor'
					);
					$this->render_view('set_impor', $data);
				}
				break;

			// Langkah 4 - Proses Impor
			case 'proses_impor':
				$dbconfig = $this->_db_config();
				$db_obj = $this->load->database($dbconfig, TRUE);

				if (!$db_obj->conn_id)
				{
					$this->_error();
				}
				else
				{
					$this->load->database();

					if ($this->install_model->import_tables(FCPATH . "install/sql/opensid.sql") === TRUE)
					{
						$this->_kosongkan_db(); // Kosongkan basisdata

						$this->db->close();
						$data = array(
							'judul' => 'pengaturan profil desa',
							'tujuan' => 'ke_desa',
							'aksi' => 'Selesai'
						);
						$this->render_view('set_data_desa', $data);
					}
					else
					{
						$data = array(
							'judul' => 'impor basisdata',
							'tujuan' => 'proses_impor',
							'aksi' => 'Impor'
						);
						$this->render_view('set_impor', $data);
					}
				}
				break;

			// Langkah 5 - Pengaturan Data Desa
			case 'ke_desa':
				$dbconfig = $this->_db_config();
				$db_obj = $this->load->database($dbconfig, TRUE);

				if (!$db_obj->conn_id)
				{
					$this->_error();
				}
				else
				{
					$this->load->database();

					$this->db->reconnect();
					$this->db->trans_off();
					$this->db->trans_begin();

					// Kosongkan tabel config desa
					$this->install_model->kosong('config');

					// Tambah config desa
					$data = array(
						'id' => 1,
						'nama_desa' => input('desa'),
						'kode_desa' => '',
						'nama_kepala_desa' => '',
						'nip_kepala_desa' => '',
						'kode_pos' => '0000',
						'nama_kecamatan' => input('kec'),
						'kode_kecamatan' => '',
						'nama_kepala_camat' => '',
						'nip_kepala_camat' => '',
						'nama_kabupaten' => input('kab'),
						'kode_kabupaten' => '',
						'nama_propinsi' => input('prov'),
						'kode_propinsi' => '',
						'website' => base_url()
					);

					$this->install_model->tambah('config', $data);

					// Setting aplikasi
					$id = 21; // key = timezone
					$data = array(
						'value' => input('timezone')
					);

					$this->install_model->ubah('setting_aplikasi', $id, $data);

					$this->db->close();

					$data = array(
						'judul' => 'pengaturan pengguna',
						'tujuan' => 'ke_pengguna',
						'aksi' => 'Selesai'
					);
					$this->render_view('set_user', $data);
				}
				break;

			// Langkah 5 - Pengaturan Pengguna
			case 'ke_pengguna':
				$dbconfig = $this->_db_config();
				$db_obj = $this->load->database($dbconfig, TRUE);

				if (!$db_obj->conn_id)
				{
					$this->_error();
				}
				else
				{
					$this->load->database();

					$this->db->reconnect();

					$pwHash = $this->generatePasswordHash(input('pass'));

					$this->db->trans_off();
					$this->db->trans_begin();

					// Kosongkan tabel user
					$this->install_model->kosong('user');

					// Tambah pengguna
					$data = array(
						'id' => 1,
						'username' => input('user'),
						'password' => $pwHash,
						'id_grup' => 1,
						'email' => 'contoh@gmail.com',
						'last_login' => date('Y-m-d H:i:s'),
						'active' => '1',
						'nama' => 'Administrator',
						'foto' => 'favicon.png',
						'session' => '',
					);

					$this->install_model->tambah('user', $data);

					if (!$this->db->trans_status())
					{
						$this->db->trans_rollback();
					}
					else
					{
						$this->db->trans_commit();

						$this->salin_contoh();

						$this->_create_file_config();

						$this->_create_file_db_config(
							array(
								'db_port' => DB_PORT,
								'db_host' => DB_HOST,
								'db_name' => DB_NAME,
								'db_user' => DB_USER,
								'db_pass' => DB_PASS
							)
						);

						$this->db->close();
						@unlink(FCPATH . "index.php");
						$this->rebuild_index();
						$this->_create_file_htaccess();
						@delete_folder(FCPATH . 'install');

						// Lakukan migrasi setelah login
						redirect(base_url('database/migrasi_db_cri'));
						$this->session->sess_destroy();
					}
				}
				break;

			// Langkah 1 - Selamat Datang
			default:
				$this->_clear();

				$data = array(
					'judul' => 'selamat datang',
					'tujuan' => 'ke_basisdata',
					'aksi' => 'Mulai'
				);
				$this->render_view('welcome', $data);
				break;
		}
	}

	protected function _cek_server($db)
	{
		$requirements = [
			'versions' => [
				'php' => '5.6',
				'mysql' => '5.6.5',
				'mariadb' => '10.1'
			],

			'config' => [
				'memory_limit' => '64M',
				'post_max_size' => '16M',
				'upload_max_filesize' => '4M',
				'max_execution_time' => '600',
				'mysql_mode' => 'strict_trans_table'
			],

			'extensions' => [
				'curl' => true,
				'dom' => true,
				'fileinfo' => true,
				'gd' => true,
				'iconv' => true,
				'intl' => true,
				'json' => true,
				'mbstring' => true,
				'pdo_mysql' => true,
				'zip' => true,
			],
		];

		$OS = php_uname('s');
		$webServer = $this->getWebServer();
		$data['webServer'] = $webServer;
		$data['OS'] = $OS;
		$data['php_ini'] = php_ini_loaded_file();
		$data['versi'] = [];
		$data['config'] = [];
		$data['extensions'] = [];
		$data['hasError'] = [];
		preg_match("#^\d.\d#", phpversion(), $versiphp);

		$php = [
			'requirement' => $requirements['versions']['php'],
			'self' => $versiphp[0],
			'err' => version_compare($versiphp[0], $requirements['versions']['php'], '>=') ? false : true
		];

		$data['versi']['php'] = $php;

		if (!extension_loaded('mysqli') || !is_callable('mysqli_connect'))
		{
			$mysql = [
				'mysql' => [
					'requirement' => $requirements['versions']['mysql']." | ".$requirements['versions']['mariadb'],
					'self' => 'Not installed',
					'err' => true
				]
			];
			$data['hasError']['mysql'] = true;
			$data['versi']['mysql'] = $mysql;
		}
		else
		{
			$result = $db->query('select version() as versi')->first_row();
			$mysql = preg_split('/-/', $result->versi);
			$versiMysql = [
				'versi' => $mysql[0],
				'distribusi' => $mysql[1]
			];

			$err = true;
			if (floatval($mysql[0]) >= 10.1 || floatval($mysql[0]) >= 5.7)
			{
				$err = false;
			}

			$mysql = [
				'requirement' => $requirements['versions'][strtolower($mysql[1])],
				'self' => $versiMysql,
				'err' => $err
			];

			if($err)
			{
				$data['hasError']['mysql'] = $err;
			}

			$data['versi']['mysql'] = $mysql;

			$resultMode = $db->query('SELECT @@sql_mode as mode')->first_row();
			$mode = explode(',', strtolower($resultMode->mode));
			$err = false;

			if (in_array($requirements['config']['mysql_mode'], $mode))
			{
				$err = true;
			}

			$config = [
				'requirement' => $requirements['config']['mysql_mode'],
				'self' => $mode,
				'err' => $err
			];

			if($err)
			{
				$data['hasError']['mysql_mode'] = $err;
			}

			$data['config']['mysql_mode'] = $config;
		}

		$vars = [
			'Client URL Library (Curl)' => 'curl',
			'Image Processing and GD' => 'gd',
			'Human Language and Character Encoding Support (Iconv)' => 'iconv',
			'Internationalization Functions (Intl)' => 'intl',
			'Multibyte String (Mbstring)' => 'mbstring',
			'JavaScript Object Notation' => 'json',
			'PDO and MySQL Functions' => 'pdo_mysql',
		];

		foreach ($vars as $label => $var)
		{
			$value = extension_loaded($var);
			$ext = [
				'requirement' => true,
				'self' => $value,
				'err' => $value ? false : true
			];

			if(!$value)
			{
				$data['hasError'][$var] = !$value;
			}

			$data['extensions'][$var] = $ext;
		}

		$vars = [
			'Document Object Model' => 'DomDocument',
			'Zip' => 'ZipArchive'
		];

		foreach ($vars as $label => $var)
		{
			$value = class_exists($var);
			$ext = [
				'requirement' => true,
				'self' => $value,
				'err' => $value ? false : true
			];

			if(!$value)
			{
				$data['hasError'][$var] = !$value;
			}

			$data['extensions'][$var] = $ext;
		}

		$vars = [
			'memory_limit',
			'post_max_size',
			'upload_max_filesize',
			'max_execution_time',
		];

		foreach ($vars as $var)
		{
			$value = ini_get($var);

			if (toBytes($value) >= toBytes($requirements['config'][$var]))
			{
				$err = false;
			}
			else
			{
				$err = true;
				if ($var === 'memory_limit')
				{
					if ($value == -1)
					{
						$err = false;
					}
				}

				if ($var === 'max_execution_time')
				{
					if ($value == 0)
					{
						$err = false;
					}
				}
			}

			$config = [
				'requirement' => $requirements['config'][$var],
				'self' => $value,
				'err' => $err
			];

			if($err)
			{
				$data['hasError'][$var] = $err;
			}

			$data['config'][$var] = $config;
		}

		return $data;
	}

	protected function getWebServer()
	{
		if (stristr($_SERVER['SERVER_SOFTWARE'], 'Apache') !== false)
		{
			return 'Apache';
		}
		elseif (stristr($_SERVER['SERVER_SOFTWARE'], 'LiteSpeed') !== false)
		{
			return 'Lite Speed';
		}
		elseif (stristr($_SERVER['SERVER_SOFTWARE'], 'Nginx') !== false)
		{
			return 'Nginx';
		}
		elseif (stristr($_SERVER['SERVER_SOFTWARE'], 'lighttpd') !== false)
		{
			return 'lighttpd';
		}
		elseif (stristr($_SERVER['SERVER_SOFTWARE'], 'IIS') !== false)
		{
			return 'Microsoft IIS';
		}

		return 'Not detected';
	}

	protected function _db_config()
	{
		define('DB_HOST', $_SESSION['db_host']);
		define('DB_PORT', $_SESSION['db_port']);
		define('DB_NAME', $_SESSION['db_name']);
		define('DB_USER', $_SESSION['db_user']);
		define('DB_PASS', $_SESSION['db_pass']);

		$config = array(
			'dsn' => 'mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME . ';charset=utf8;',
			'hostname' => DB_HOST,
			'username' => DB_USER,
			'password' => DB_PASS,
			'database' => DB_NAME,
			'dbdriver' => 'pdo',
			'dbprefix' => '',
			'pconnect' => FALSE,
			'db_debug' => (ENVIRONMENT !== 'development'),
			'cache_on' => FALSE,
			'cachedir' => '',
			'char_set' => 'utf8',
			'dbcollat' => 'utf8_general_ci',
			'swap_pre' => '',
			'encrypt' => FALSE,
			'compress' => FALSE,
			'stricton' => FALSE,
			'failover' => array(),
			'save_queries' => FALSE
		);

		return $config;
	}

	protected function _create_file_config()
	{
		$content = cfile();
		$file = FCPATH . 'desa/config/config.php';
		write_file($file, $content);
	}

	protected function _create_file_db_config($configs)
	{
		$content = cdb($configs);
		$file = FCPATH . 'desa/config/database.php';
		write_file($file, $content);
	}

	private function _create_file_htaccess()
	{
		if (file_exists(FCPATH . '.htaccess')) {
			@unlink(FCPATH . '.htaccess');
		}

		$content = htaccess();
		$file = FCPATH . '.htaccess';
		write_file($file, $content);
	}

	protected function rebuild_index()
	{
		$content = cindex();
		$file = FCPATH . "index.php";
		write_file(FCPATH . "index.php", $content);
	}

	// Hapus fungsi ini jika installer sudah digabungkan dengan opensid
	private function generatePasswordHash($string)
	{
		$string = is_string($string) ? $string : strval($string);
		$pwHash = password_hash($string, PASSWORD_BCRYPT);

		if (password_needs_rehash($pwHash, PASSWORD_BCRYPT))
		{
			$pwHash = password_hash($string, PASSWORD_BCRYPT);
		}

		return $pwHash;
	}

	// Hapus fungsi ini jika installer sudah digabungkan dengan opensid
	private function salin_contoh()
	{
		if (!file_exists('desa'))
		{
			mkdir('desa');
			xcopy('desa-contoh', 'desa');
		}
	}

	private function _error()
	{
		$data = array(
			'judul' => 'gagal koneksi basisdata',
			'tujuan' => 'ke_basisdata',
			'aksi' => 'Coba lagi'
		);
		$this->render_view('disconnect', $data);
	}

	private function _clear()
	{
		$_SESSION['db_host'] = 'localhost';
		$_SESSION['db_port'] = '3306';
		$_SESSION['db_name'] = $_SESSION['db_user'] = $_SESSION['db_pass'] = '';
	}

	private function _kosongkan_db()
	{
		if (empty(input('kosongkan')))
		{
			$this->install_model->kosongkan_db();
		}
	}

}
