<?php

/**
 * Pesan validasi default berbahasa Indonesia.
 * Pesan spesifik per form (dari $messages Livewire) tetap didefinisikan di controller.
 */
return [
    // Core Messages
    'noRuleSets'      => 'Tidak ada aturan validasi yang ditentukan.',
    'ruleNotFound'    => '"{0}" bukan aturan validasi yang valid.',
    'groupNotFound'   => '"{0}" bukan grup aturan validasi.',
    'groupNotArray'   => 'Grup aturan "{0}" harus berupa array.',
    'invalidTemplate' => '"{0}" bukan template validasi yang valid.',

    // Rule Messages
    'alpha'                 => '{field} hanya boleh berisi huruf.',
    'alpha_dash'            => '{field} hanya boleh berisi huruf, angka, garis bawah, dan tanda hubung.',
    'alpha_numeric'         => '{field} hanya boleh berisi huruf dan angka.',
    'alpha_numeric_punct'   => '{field} hanya boleh berisi huruf, angka, spasi, dan karakter ~ ! # $ % & * - _ + = | : .',
    'alpha_numeric_space'   => '{field} hanya boleh berisi huruf, angka, dan spasi.',
    'alpha_space'           => '{field} hanya boleh berisi huruf dan spasi.',
    'decimal'               => '{field} harus berupa angka desimal.',
    'differs'               => '{field} harus berbeda dengan {param}.',
    'equals'                => '{field} harus sama dengan: {param}.',
    'exact_length'          => '{field} harus tepat {param} karakter.',
    'field_exists'          => '{field} wajib ada.',
    'greater_than'          => '{field} harus lebih besar dari {param}.',
    'greater_than_equal_to' => '{field} minimal {param}.',
    'hex'                   => '{field} hanya boleh berisi karakter heksadesimal.',
    'in_list'               => '{field} harus salah satu dari: {param}.',
    'integer'               => '{field} harus berupa bilangan bulat.',
    'is_natural'            => '{field} hanya boleh berisi angka.',
    'is_natural_no_zero'    => '{field} hanya boleh berisi angka dan harus lebih dari nol.',
    'is_not_unique'         => '{field} tidak ditemukan.',
    'is_unique'             => '{field} sudah digunakan.',
    'less_than'             => '{field} harus lebih kecil dari {param}.',
    'less_than_equal_to'    => '{field} maksimal {param}.',
    'matches'               => '{field} tidak cocok dengan {param}.',
    'max_length'            => '{field} maksimal {param} karakter.',
    'min_length'            => '{field} minimal {param} karakter.',
    'not_equals'            => '{field} tidak boleh: {param}.',
    'not_in_list'           => '{field} tidak boleh salah satu dari: {param}.',
    'numeric'               => '{field} harus berupa angka.',
    'regex_match'           => 'Format {field} tidak valid.',
    'required'              => '{field} wajib diisi.',
    'required_with'         => '{field} wajib diisi jika {param} diisi.',
    'required_without'      => '{field} wajib diisi jika {param} kosong.',
    'string'                => '{field} harus berupa teks.',
    'timezone'              => '{field} harus berupa zona waktu yang valid.',
    'valid_base64'          => '{field} harus berupa string base64 yang valid.',
    'valid_email'           => 'Format {field} tidak valid.',
    'valid_emails'          => 'Semua alamat pada {field} harus valid.',
    'valid_ip'              => '{field} harus berupa alamat IP yang valid.',
    'valid_url'             => '{field} harus berupa URL yang valid.',
    'valid_url_strict'      => '{field} harus berupa URL yang valid.',
    'valid_date'            => '{field} harus berupa tanggal yang valid.',
    'valid_json'            => '{field} harus berupa JSON yang valid.',

    // Credit Cards
    'valid_cc_number' => '{field} bukan nomor kartu kredit yang valid.',

    // Files
    'uploaded' => '{field} bukan file unggahan yang valid.',
    'max_size' => 'Ukuran {field} terlalu besar.',
    'is_image' => '{field} harus berupa gambar.',
    'mime_in'  => 'Tipe file {field} tidak didukung.',
    'ext_in'   => 'Ekstensi file {field} tidak didukung.',
    'max_dims' => '{field} bukan gambar, atau dimensinya terlalu besar.',
    'min_dims' => '{field} bukan gambar, atau dimensinya terlalu kecil.',
];
