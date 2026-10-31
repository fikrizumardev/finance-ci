<?php

namespace App\Models;

use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\Model;

/**
 * Model untuk tabel yang memiliki kolom user_id. Semua akses data domain
 * wajib melewati forUser()/findOwned() agar data antar user tidak bocor.
 */
abstract class UserScopedModel extends Model
{
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';

    /**
     * Batasi query berikutnya ke data milik user tertentu.
     *
     * @return $this
     */
    public function forUser(int $userId): static
    {
        return $this->where($this->table . '.user_id', $userId);
    }

    /**
     * Ambil satu record milik user, atau null jika tidak ada / milik user lain.
     */
    public function findOwned(int|string|null $id, int $userId): ?object
    {
        if ($id === null || $id === '' || ! ctype_digit((string) $id)) {
            return null;
        }

        return $this->forUser($userId)->find((int) $id);
    }

    /**
     * Sama dengan findOwned(), tetapi melempar 404 jika tidak ditemukan.
     */
    public function findOwnedOrFail(int|string|null $id, int $userId): object
    {
        return $this->findOwned($id, $userId) ?? throw PageNotFoundException::forPageNotFound();
    }
}
