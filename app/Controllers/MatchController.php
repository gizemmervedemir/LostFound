<?php

namespace App\Controllers;

use App\Models\Item;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class MatchController extends Controller
{
    private $item;

    public function __construct(Item $item)
    {
        $this->item = $item;
    }

    /**
     * Create a new match
     */
    public function store(Request $request)
    {
        if (!$this->isAuthenticated()) {
            return $this->redirect('login');
        }

        $validated = $this->validate($request, [
            'item_id' => 'required|integer|exists:items,id',
            'matched_item_id' => 'required|integer|exists:items,id',
            'message' => 'required|string|max:1000'
        ]);

        // Verify user owns the item
        $item = $this->item->getById($validated['item_id']);
        if (!$item || $item['user_id'] !== Session::get('user_id')) {
            return $this->handleError('Unauthorized');
        }

        // Insert match record
        $stmt = $this->db->prepare("
            INSERT INTO matches (item_id, matched_item_id, status, message) 
            VALUES (?, ?, ?, ?)
        ");
        
        $stmt->execute([
            $validated['item_id'],
            $validated['matched_item_id'],
            'pending',
            $validated['message']
        ]);

        // Send email notification to both parties
        $this->sendMatchNotification($validated['item_id'], $validated['matched_item_id']);

        return $this->handleSuccess('Eşleşme isteği gönderildi', 'items.index');
    }

    /**
     * Update match status
     */
    public function update(Request $request, $id)
    {
        if (!$this->isAuthenticated()) {
            return $this->redirect('login');
        }

        $validated = $this->validate($request, [
            'status' => 'required|in:confirmed,rejected'
        ]);

        // Verify user is involved in the match
        $stmt = $this->db->prepare("
            SELECT m.*, i1.user_id as user1_id, i2.user_id as user2_id
            FROM matches m
            JOIN items i1 ON m.item_id = i1.id
            JOIN items i2 ON m.matched_item_id = i2.id
            WHERE m.id = ?
        ");
        
        $stmt->execute([$id]);
        $match = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$match || ($match['user1_id'] !== Session::get('user_id') && $match['user2_id'] !== Session::get('user_id'))) {
            return $this->handleError('Unauthorized');
        }

        // Update match status
        $stmt = $this->db->prepare("UPDATE matches SET status = ? WHERE id = ?");
        $stmt->execute([$validated['status'], $id]);

        // Update item status if confirmed
        if ($validated['status'] === 'confirmed') {
            $stmt = $this->db->prepare("UPDATE items SET status = 'matched' WHERE id IN (?, ?)");
            $stmt->execute([$match['item_id'], $match['matched_item_id']]);
        }

        // Send email notification
        $this->sendMatchStatusUpdate($id, $validated['status']);

        return $this->handleSuccess('Eşleşme durumu güncellendi', 'items.index');
    }

    /**
     * Send match notification email
     */
    private function sendMatchNotification($itemId, $matchedItemId)
    {
        // Get items and users information
        $stmt = $this->db->prepare("
            SELECT i1.*, i2.*, u1.email as user1_email, u2.email as user2_email
            FROM items i1
            JOIN items i2 ON i1.id = ? AND i2.id = ?
            JOIN users u1 ON u1.id = i1.user_id
            JOIN users u2 ON u2.id = i2.user_id
        ");
        
        $stmt->execute([$itemId, $matchedItemId]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        // Send email to both parties
        $this->sendEmail(
            $data['user1_email'],
            'Yeni Eşleşme İsteği',
            'Yeni bir eşleşme isteği aldınız: ' . $data['title']
        );

        $this->sendEmail(
            $data['user2_email'],
            'Eşleşme İsteği Gönderildi',
            'Eşleşme isteği gönderildi: ' . $data['title']
        );
    }

    /**
     * Send match status update email
     */
    private function sendMatchStatusUpdate($matchId, $status)
    {
        // Get match information
        $data = query("
            SELECT m.*, i1.*, i2.*, u1.email as user1_email, u2.email as user2_email
            FROM matches m
            JOIN items i1 ON m.item_id = i1.id
            JOIN items i2 ON m.matched_item_id = i2.id
            JOIN users u1 ON u1.id = i1.user_id
            JOIN users u2 ON u2.id = i2.user_id
            WHERE m.id = ?
        ", [$matchId])[0];

        $statusText = $status === 'confirmed' ? 'onaylandı' : 'reddedildi';
        
        // Send email to both parties
        $this->sendEmail(
            $data['user1_email'],
            'Eşleşme Durumu Güncellendi',
            'Eşleşme isteği ' . $statusText . ': ' . $data['title']
        );

        $this->sendEmail(
            $data['user2_email'],
            'Eşleşme Durumu Güncellendi',
            'Eşleşme isteği ' . $statusText . ': ' . $data['title']
        );
    }

    /**
     * Send email
     */
    private function sendEmail($to, $subject, $message)
    {
        $mail = new PHPMailer(true);
        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host = $_ENV['MAIL_HOST'];
            $mail->SMTPAuth = true;
            $mail->Username = $_ENV['MAIL_USERNAME'];
            $mail->Password = $_ENV['MAIL_PASSWORD'];
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = $_ENV['MAIL_PORT'];

            // Recipients
            $mail->setFrom($_ENV['MAIL_FROM_ADDRESS'], $_ENV['MAIL_FROM_NAME']);
            $mail->addAddress($to);

            // Content
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $message;

            $mail->send();
        } catch (Exception $e) {
            error_log("Email could not be sent. Mailer Error: {$mail->ErrorInfo}");
        }
    }
}
