<?php

declare(strict_types=1);

namespace B13\OnlineMediaUpdater\Domain\Repository;

use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class FileRepository
{
    private const SYS_FILE_TABLE = 'sys_file';

    public function getVideosByFileExtension(string $extension, int $limit = 0): array
    {
        $queryBuilder = $this->getQueryBuilder(self::SYS_FILE_TABLE);

        $whereConstraints = [];
        $whereConstraints[] = $queryBuilder->expr()->eq(
            'extension',
            $queryBuilder->createNamedParameter(strtolower($extension))
        );
        $whereConstraints[] = $queryBuilder->expr()->eq(
            'missing',
            $queryBuilder->createNamedParameter(0, Connection::PARAM_INT)
        );

        $statement = $queryBuilder
            ->select('*')
            ->addSelectLiteral('RAND() AS randomnumber')
            ->from(self::SYS_FILE_TABLE)
            ->where(...$whereConstraints)
            ->orderBy('randomnumber');

        if ($limit > 0) {
            $statement->setMaxResults($limit);
        }

        return $queryBuilder->executeQuery()->fetchAllAssociative();
    }

    /**
     * @param string $tableName
     * @return QueryBuilder
     */
    protected function getQueryBuilder(string $tableName = ''): QueryBuilder
    {
        $connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
        $connection = $connectionPool->getConnectionForTable($tableName);

        return $connection->createQueryBuilder();
    }
}
