<?php
namespace models;
use PDO;


use core\models\DataBase;

/**
 * @description Class MessageDB, used to manage the messages in the database
 */
class MessageDB extends DataBase {

    protected static $instance;

    /**
     * @description
     * - Get the conversation id between a user and an offer, if it exists
     * - If it doesn't exist, return null
     * - This is used to know if the user has already started a conversation with the offer or not
     * @param string $email User email
     * @param int $ouid Offer unique Id
     * @return int|null
     */
    public function getConversationId(string $email, int $ouid): ?int
    {
        $query = $this->dbConn->prepare('SELECT id_conv 
                                               FROM transactions
                                               WHERE (email = :email AND ouid = :ouid)');
        $query->bindValue('email', $email);
        $query->bindValue('ouid', $ouid);
        $query->execute();
        $result = $query->fetch(PDO::FETCH_ASSOC);
        return $result ? (int)$result['id_conv'] : null;
    }

    /**
     * @description
     * - Check if the user is allowed to chat in the conversation.
     * - A user is allowed to chat if he is the buyer or the seller
     *   of the offer associated to the conversation.
     * @param string $userEmail User email
     * @param int $id_conv Conversation id
     * @return bool
     */
    public function allowedToChat(string $userEmail, int $id_conv): bool
    {
        $query = $this->dbConn->prepare('SELECT COUNT(*) 
                                               FROM transactions t
                                               JOIN offer o ON t.ouid = o.ouid
                                               WHERE t.id_conv = :id_conv
                                               AND (t.email = :userEmail OR o.owner = :userEmail)');
        $query->bindValue('userEmail', $userEmail);
        $query->bindValue('id_conv', $id_conv);
        $query->execute();
        return $query->fetchColumn() > 0;
    }

    /**
     * @description
     * - Get all the messages of a conversation, with the username of the sender
     *   and the date of the message.
     * @param int $id_conv
     * @return array<mixed>
 */
    public function getMessagesByConversation(int $id_conv): array
    {
        $query = $this->dbConn->prepare('SELECT m.content, m.date_msg, m.email, u.username
                                               FROM message m
                                               JOIN user_ u ON m.email = u.email
                                               WHERE m.id_conv = :id_conv
                                               ORDER BY m.date_msg ASC');
        $query->bindValue('id_conv', $id_conv);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * @description
     * - Add a message to the conversation, with the current date and time.
     * @param int $id_conv Conversation id
     * @param string $email User email of the sender
     * @param string $content Content of the message
     * @return array<mixed>
     */

    public function addMessage(int $id_conv, string $email, string $content) : array {
        $query = $this->dbConn->prepare('INSERT INTO message (id_conv, email, content, date_msg) 
                                               VALUES (:id_conv, :email, :content, NOW())');
        $query->bindValue('id_conv', $id_conv);
        $query->bindValue('email', $email);
        $query->bindValue('content', $content);
        $query->execute();
        return [
            'id_conv' => $id_conv,
            'email' => $email,
            'content' => $content,
        ];
    }

    /**
     * @description
     * - Get all the conversations of a user, with the offer title
     *  and the username of the buyer and the seller.
     * @param string $email User email
     * @return array<mixed>
     */
    public function getUserConversations(string $email) : array {
        $query = $this->dbConn->prepare ('SELECT c.id_conv, t.ouid, t.email AS buyer_email, u_buyer.username AS buyer_name,
                                                o.title AS offer_title, o.owner AS seller_email, u_seller.username AS seller_name
                                                FROM conversation c 
                                                JOIN transactions t ON c.id_conv = t.id_conv
                                                JOIN offer o ON t.ouid = o.ouid
                                                JOIN user_ u_buyer ON t.email = u_buyer.email
                                                JOIN user_ u_seller ON o.owner = u_seller.email
                                                WHERE t.email = :email OR o.owner = :email');
        $query->bindValue('email', $email);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }
}