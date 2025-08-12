<?php declare(strict_types=1);

namespace App\Repositories;

use App\Interfaces\RepositoryInterface;
use App\Config\Database;
use App\Entities\Article;
use App\Entities\Author;
use PDO;

class ArticleRepository implements RepositoryInterface
{
    private PDO $db;
    private AuthorRepository $authorRepo;

    public function __construct()
    {
        $this->db = Database::getConnection();
        $this->authorRepo = new AuthorRepository();
    }

    public function findAll(): array
    {
        $stmt = $this->db->query("CALL sp_article_list();");
        $rows = $stmt->fetchAll();
        $stmt->closeCursor();

        $out = [];
        foreach ($rows as $r) {
            $out[] = $this->hydrate($r);
        }
        return $out;
    }

    public function create(object $entity): bool
    {
        if (!$entity instanceof Article) 
        {
            throw new \InvalidArgumentException("Article expected");
        }

        $stmt = $this->db->prepare("CALL sp_create_article(:title, :description, :publicationDate, :authorId, :doi, :abstract, :keywords, :indexation, :magazine, :area);");
        $ok = $stmt->execute([
            ':title'           => $entity->getTitle(),
            ':description'     => $entity->getDescription(),
            ':publicationDate' => $entity->getPublicationDate()->format('Y-m-d'),
            ':authorId'        => $entity->getAuthor()->getId(),
            ':doi'             => $entity->getDoi(),
            ':abstract'        => $entity->getAbstract(),
            ':keywords'        => $entity->getKeywords(),
            ':indexation'      => $entity->getIndexation(),
            ':magazine'        => $entity->getMagazine(),
            ':area'            => $entity->getArea()
        ]);

        if ($ok) {
            $stmt->fetch();
        }
        $stmt->closeCursor();

        return $ok;
    }

    public function findById(int $id): ?object
    {
        $stmt = $this->db->prepare("CALL sp_find_article(:id);");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        $stmt->closeCursor();

        return $row ? $this->hydrate($row) : null;
    }

    public function update(object $entity): bool
    {
        if (!$entity instanceof Article) 
        {
            throw new \InvalidArgumentException("Article expected");
        }

        $stmt = $this->db->prepare("CALL sp_update_article(:id, :title, :description, :publicationDate, :authorId, :doi, :abstract, :keywords, :indexation, :magazine, :area);");
        $ok = $stmt->execute([
            ':id'              => $entity->getId(),
            ':title'           => $entity->getTitle(),
            ':description'     => $entity->getDescription(),
            ':publicationDate' => $entity->getPublicationDate()->format('Y-m-d'),
            ':authorId'        => $entity->getAuthor()->getId(),
            ':doi'             => $entity->getDoi(),
            ':abstract'        => $entity->getAbstract(),
            ':keywords'        => $entity->getKeywords(),
            ':indexation'      => $entity->getIndexation(),
            ':magazine'        => $entity->getMagazine(),
            ':area'            => $entity->getArea()
        ]);

        if ($ok) {
            $stmt->fetch();
        }
        $stmt->closeCursor();

        return $ok;
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("CALL sp_delete_article(:id);");
        $ok = $stmt->execute([':id' => $id]);

        if ($ok) {
            $stmt->fetch();
        }
        $stmt->closeCursor();

        return $ok;
    }

    public function hydrate(array $row): Article
    {
        $author = new Author(
            (int) ($row['id'] ?? 0),
            $row['first_name'] ?? '',
            $row['last_name'] ?? '',
            $row['username'] ?? '',
            $row['email'] ?? '',
            'temporal',
            $row['orcid'] ?? '',
            $row['affiliation'] ?? ''
        );

        if (isset($row['password'])) {
            $ref = new \ReflectionClass($author);
            $property = $ref->getProperty('password');
            $property->setAccessible(true);
            $property->setValue($author, $row['password']);
        }

        $publicationId = isset($row['publication_id']) ? (int) $row['publication_id'] : 0;

        return new Article(
            $publicationId,
            $row['title'] ?? '',
            $row['description'] ?? '',
            new \DateTime($row['publication_date'] ?? 'now'),
            $author,
            $row['doi'] ?? '',
            $row['abstract'] ?? '',
            $row['keywords'] ?? '',
            $row['indexation'] ?? '',
            $row['magazine'] ?? '',
            $row['area'] ?? ''
        );
    }
}
