<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Install_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    public function last_id()
    {
        return $this->db->insert_id();
    }

    public function import_tables($file)
    {
        $this->db->trans_off();

        $this->db->trans_start(TRUE);
        $this->db->trans_begin();

        $sql = file_get_contents($file);
        $this->db->query($sql);

        if ($this->db->trans_status() === TRUE) {
            $this->db->trans_commit();
            return true;
        } else {
            $this->db->trans_rollback();
            return false;
        }
    }

    public function tambah($tabel, $data)
    {
        $this->db->insert($tabel, $data);
    }

    public function kosong($tabel)
    {
        $this->db->truncate($tabel);
    }

    public function ubah($tabel, $id, $data)
    {
        $this->db->where('id', $id)->update($tabel, $data);
    }

    // Hapus fungsi ini jika installer sudah digabungkan dengan opensid
    public function kosongkan_db()
    {
        // Views tidak perlu dikosongkan.
        $views = array(
            "daftar_anggota_grup",
            "daftar_grup",
            "daftar_kontak",
            "dokumen_hidup",
            "keluarga_aktif",
            "penduduk_hidup"
        );

        $table_lookup = array(
            "analisis_ref_state",
            "analisis_ref_subjek",
            "analisis_tipe_indikator",
            "artikel", //remove everything except widgets 1003
            "gis_simbol",
            "klasifikasi_surat",
            "media_sosial", //?
            "provinsi",
            "ref_dokumen",
            "ref_pindah",
            "ref_syarat_surat",
            "setting_modul",
            "setting_aplikasi",
            "setting_aplikasi_options",
            "skin_sid",
            "syarat_surat",
            "tweb_aset",
            "tweb_cacat",
            "tweb_cara_kb",
            "tweb_golongan_darah",
            "tweb_keluarga_sejahtera",
            "tweb_penduduk_agama",
            "tweb_penduduk_asuransi",
            "tweb_penduduk_hubungan",
            "tweb_penduduk_kawin",
            "tweb_penduduk_pekerjaan",
            "tweb_penduduk_pendidikan",
            "tweb_penduduk_pendidikan_kk",
            "tweb_penduduk_sex",
            "tweb_penduduk_status",
            "tweb_penduduk_umur",
            "tweb_penduduk_warganegara",
            "tweb_rtm_hubungan",
            "tweb_sakit_menahun",
            "tweb_status_dasar",
            "tweb_status_ktp",
            "tweb_surat_format",
            "user",
            "user_grup",
            "widget"
        );

        // Hanya kosongkan contoh menu kalau pengguna memilih opsi itu
        //if (empty($_POST['kosongkan_menu']))
        //{
            array_push($table_lookup,"kategori","menu");
        //}

        $jangan_kosongkan = array_merge($views, $table_lookup);

        // Hapus semua artikel kecuali artikel widget dengan kategori 1003
        $this->db->where("id_kategori !=", "1003");
        $query = $this->db->delete('artikel');
        // Kosongkan semua tabel kecuali table lookup dan views
        // Tabel yang ada foreign key akan dikosongkan secara otomatis
        $semua_table = $this->db->list_tables();
        $this->db->simple_query('SET FOREIGN_KEY_CHECKS=0');
        foreach ($semua_table as $table)
        {
            if (!in_array($table, $jangan_kosongkan))
            {
                $query = "DELETE FROM " . $table . " WHERE 1";
                $this->db->query($query);
            }
        }
        $this->db->simple_query('SET FOREIGN_KEY_CHECKS=1');
        // Tambahkan kembali Analisis DDK Profil Desa dan Analisis DAK Profil Desa
        $file_analisis = FCPATH . 'assets/import/analisis_DDK_Profil_Desa.xls';
        $this->import_excel($file_analisis, 'DDK02', $jenis = 1);
        $file_analisis = FCPATH . 'assets/import/analisis_DAK_Profil_Desa.xls';
        $this->import_excel($file_analisis, 'DAK02', $jenis = 1);
    }

    // Hapus fungsi ini jika installer sudah digabungkan dengan opensid
    public function import_excel($file='', $kode='00000', $jenis=2)
    {
        $this->load->library('Spreadsheet_Excel_Reader');

        if (empty($file)) $file = $_FILES['userfile']['tmp_name'];
        $data = new Spreadsheet_Excel_Reader($file);
        $sheet=0;

        $master['nama'] = $data->val(1, 2, $sheet);
        $master['subjek_tipe'] = $data->val(2, 2, $sheet);
        $master['lock'] = $data->val(3, 2, $sheet);
        $master['pembagi'] = $data->val(4, 2, $sheet);
        $master['deskripsi'] = $data->val(5, 2, $sheet);
        $master['kode_analisis'] = $kode;
        $master['jenis'] = $jenis;

        $outp = $this->db->insert('analisis_master',$master);
        $id_master = $this->db->insert_id();

        $periode['id_master']   = $id_master;
        $periode['nama'] = $data->val(6, 2, $sheet);
        $periode['tahun_pelaksanaan']   = $data->val(7, 2, $sheet);
        $periode['keterangan'] = $data->val(5, 2, $sheet);
        $periode['aktif']   = 1;
        $this->db->insert('analisis_periode', $periode);

        $sheet = 1;
        $baris = $data->rowcount($sheet_index=$sheet);
        $kolom = $data->colcount($sheet_index=$sheet);

        for ($i=2; $i<=$baris; $i++)
        {
            $sql = "SELECT * FROM analisis_kategori_indikator WHERE kategori=? AND id_master=?";
            $query = $this->db->query($sql, array($data->val($i, 3, $sheet), $id_master));
            $cek = $query->row_array();

            if (!$cek)
            {
                $kategori['id_master'] = $id_master;
                $kategori['kategori']   = $data->val($i, 3, $sheet);
                $this->db->insert('analisis_kategori_indikator', $kategori);
            }
        }

        for ($i=2; $i<=$baris; $i++)
        {
            $indikator['id_master'] = $id_master;
            $indikator['nomor'] = $data->val($i, 1, $sheet);
            $indikator['pertanyaan'] = $data->val($i, 2, $sheet);

            $sql = "SELECT * FROM analisis_kategori_indikator WHERE kategori=? AND id_master=?";
            $query = $this->db->query($sql, array($data->val($i, 3, $sheet), $id_master));
            $kategori = $query->row_array();

            $indikator['id_kategori']   = $kategori['id'];
            $indikator['id_tipe']   = $data->val($i, 4, $sheet);
            $indikator['bobot'] = $data->val($i, 5, $sheet) ?: 0;
            $indikator['act_analisis'] = $data->val($i, 6, $sheet) ?: 2;

            $this->db->insert('analisis_indikator', $indikator);
        }

        $sheet = 2;
        $baris = $data->rowcount($sheet_index=$sheet);
        $kolom = $data->colcount($sheet_index=$sheet);

        for ($i=2; $i<=$baris; $i++)
        {
            $kode   = explode(".", $data->val($i, 3, $sheet));

            $parameter['kode_jawaban'] = $data->val($i, 2, $sheet);
            $parameter['jawaban']   = $data->val($i, 3, $sheet);

            $sql = "SELECT id FROM analisis_indikator WHERE nomor=? AND id_master=?";
            $query = $this->db->query($sql, array($data->val($i, 1, $sheet), $id_master));
            $indikator = $query->row_array();

            $parameter['id_indikator'] = $indikator['id'];
            $parameter['nilai'] = $data->val($i, 4, $sheet) ?: 0;
            $parameter['asign'] = 1;

            $this->db->insert('analisis_parameter',$parameter);
        }

        $sheet = 3;
        $baris = $data->rowcount($sheet_index=$sheet);
        $kolom = $data->colcount($sheet_index=$sheet);

        for ($i=2; $i<=$baris; $i++)
        {
            $klasifikasi['id_master']   = $id_master;
            $klasifikasi['nama'] = $data->val($i, 1, $sheet);
            $klasifikasi['minval'] = $data->val($i, 2, $sheet);
            $klasifikasi['maxval'] = $data->val($i, 3, $sheet);

            $this->db->insert('analisis_klasifikasi', $klasifikasi);
        }

        return $id_master;
    }
}
