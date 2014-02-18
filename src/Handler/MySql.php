<?php
namespace Panadas\Auth\Handler;

use Panadas\Auth\User\UserInterface;

class MySql extends AbstractPdo
{

    public function create(UserInterface $user, $lifetime = null)
    {
        $db = $this->getPdo();

        $stmt = $db->prepare(
            "
                SELECT COUNT(`token`)
                FROM `{$this->getTableName()}`
                WHERE `token` = :token
            "
        );

        $unique = false;

        while (!$unique) {

            $token = $this->createToken($user);

            $stmt->bindValue(":token", $token);
            $stmt->execute();

            $unique = ($stmt->fetchColumn() == 0);

        }

        $stmt = $db->prepare(
            "
                INSERT INTO `{$this->getTableName()}` (
                    `token`,
                    `user_id`,
                    `lifetime`,
                    `created`,
                    `modified`
                ) VALUES (
                    :token,
                    :userId,
                    :lifetime,
                    :created,
                    `created`
                );
            "
        );

        $stmt->bindValue(":token", $token);
        $stmt->bindValue(":userId", $user->getId(), $db::PARAM_INT);
        $stmt->bindValue(":created", (new \DateTime())->format("Y-m-d H:i:s"));

        if (null === $lifetime) {
            $stmt->bindValue(":lifetime", null, $db::PARAM_NULL);
        } else {
            $stmt->bindValue(":lifetime", $lifetime, $db::PARAM_INT);
        }

        $stmt->execute();

        return $token;
    }

    public function retrieve($token)
    {
        $stmt = $this->getPdo()->prepare(
            "
                SELECT `user_id`
                FROM `{$this->getTableName()}`
                WHERE `token` = :token
            "
        );

        $stmt->bindValue(":token", $token);
        $stmt->execute();

        $result = $stmt->fetchColumn();

        if (false === $result) {
            return null;
        }

        return $result;
    }

    public function update($token, \DateTime $modified)
    {
        $stmt = $this->getPdo()->prepare(
            "
                UPDATE `{$this->getTableName()}`
                SET `modified` = :modified
                WHERE `token` = :token
            "
        );

        $stmt->bindValue(":token", $token);
        $stmt->bindValue(":modified", $modified->format("Y-m-d H:i:s"));
        $stmt->execute();

        return $this;
    }

    public function delete($token)
    {
        $stmt = $this->getPdo()->prepare(
            "
                DELETE FROM `{$this->getTableName()}`
                WHERE `token` = :token
            "
        );

        $stmt->bindValue(":token", $token);
        $stmt->execute();

        return $this;
    }

    public function gc()
    {
        $stmt = $this->getPdo()->prepare(
            "
                DELETE FROM `{$this->getTableName()}`
                WHERE (
                    `lifetime` IS NOT NULL
                    AND (DATE_ADD(`modified`, INTERVAL `lifetime` SECOND) <= :now)
                )
            "
        );

        $stmt->bindValue(":now", (new \DateTime())->format("Y-m-d H:i:s"));
        $stmt->execute();

        return $this;
    }
}
