<?php
class AuthorsModel
{
    protected static ?PDO $pdo = null;

    public static function init(?PDO $pdo = null)
    {
        if ($pdo instanceof PDO) {
            self::$pdo = $pdo;
            return;
        }
        if (class_exists('Database')) {
            try {
                self::$pdo = Database::getInstance();
                return;
            } catch (Exception $e) {
            }
        }
    }

    protected static function getPdo(): PDO
    {
        if (self::$pdo instanceof PDO) return self::$pdo;
        if (class_exists('Database')) {
            try {
                self::$pdo = Database::getInstance();
                return self::$pdo;
            } catch (Exception $e) {
            }
        }
        throw new RuntimeException('PDO not initialized for AuthorsModel');
    }


    public static function getAllAuthors()
    {
        $pdo = self::getPdo();
        // return cache_remember('all_authors', 3600, function () use ($pdo) {
        //     $stmt = $pdo->query("SELECT * FROM tacgia ORDER BY ho, tendem, ten");
        //     return $stmt->fetchAll(PDO::FETCH_ASSOC);
        // });
        $stmt = $pdo->query("SELECT * FROM tacgia ORDER BY ho, tendem, ten");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getAuthorById($tacgia_id)
    {
        $allAuthors = self::getAllAuthors();
        foreach ($allAuthors as $author) {
            if ($author['tacgia_id'] == $tacgia_id) return $author;
        }
        return null;
    }

    public static function addAuthor($data)
    {
        $pdo = self::getPdo();
        $stmt = $pdo->prepare("INSERT INTO tacgia (ho, tendem, ten, diachi, sdt, email) VALUES (:ho, :tendem, :ten, :diachi, :sdt, :email)");
        $result = $stmt->execute([
            ':ho'     => $data['ho'],
            ':tendem' => $data['tendem'] ?? null,
            ':ten'    => $data['ten'],
            ':diachi' => $data['diachi'] ?? null,
            ':sdt'    => $data['sdt'] ?? null,
            ':email'  => $data['email'] ?? null
        ]);
        if ($result) cache_forget('all_authors');
        return $result;
    }

    public static function updateAuthor($tacgia_id, $data)
    {
        $pdo = self::getPdo();
        $stmt = $pdo->prepare("UPDATE tacgia SET ho = :ho, tendem = :tendem, ten = :ten, diachi = :diachi, sdt = :sdt, email = :email WHERE tacgia_id = :id");
        $result = $stmt->execute([
            ':ho'     => $data['ho'],
            ':tendem' => $data['tendem'] ?? null,
            ':ten'    => $data['ten'],
            ':diachi' => $data['diachi'] ?? null,
            ':sdt'    => $data['sdt'] ?? null,
            ':email'  => $data['email'] ?? null,
            ':id'     => $tacgia_id
        ]);
        if ($result) cache_forget('all_authors');
        return $result;
    }

    public static function deleteAuthor($tacgia_id)
    {
        $pdo = self::getPdo();
        $stmt = $pdo->prepare("DELETE FROM tacgia WHERE tacgia_id = :id");
        $result = $stmt->execute([':id' => $tacgia_id]);
        if ($result) cache_forget('all_authors');
        return $result;
    }
}
