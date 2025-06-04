<?php

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use Ratchet\App; // Add this line to import the App class from Ratchet

require '../vendor/autoload.php'; // Correct path to autoload.php

class ChatServer implements MessageComponentInterface
{
    private $clients;

    public function __construct()
    {
        $this->clients = new \SplObjectStorage(); // Use fully qualified namespace for SplObjectStorage
    }

    public function onOpen(ConnectionInterface $conn)
    {
        // Store the new connection
        $this->clients->attach($conn);
        echo "New connection: {$conn->resourceId}\n";
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        $data = json_decode($msg, true);

        // Store the message in the database
        try {
            $pdo = new \PDO('mysql:host=localhost;dbname=your_db', 'username', 'password');
            $stmt = $pdo->prepare("INSERT INTO chat_messages (event_id, sender_id, receiver_id, message) VALUES (?, ?, ?, ?)");
            $stmt->execute([$data['event_id'], $data['sender_id'], $data['receiver_id'], $data['message']]);
        } catch (\PDOException $e) {
            echo "Database error: {$e->getMessage()}\n";
        }

        // Broadcast the message to other clients
        foreach ($this->clients as $client) {
            if ($from !== $client) {
                $client->send($msg);
            }
        }
    }

    public function onClose(ConnectionInterface $conn)
    {
        // Remove the connection
        $this->clients->detach($conn);
        echo "Connection {$conn->resourceId} has disconnected\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        echo "Error: {$e->getMessage()}\n";
        $conn->close();
    }
}

// Run the server
$server = new App('localhost', 8080, '0.0.0.0'); // Use the "new" keyword to instantiate the App class
$server->route('/chat', new ChatServer(), ['*']); // Define the WebSocket route and allow all origins
$server->run();
