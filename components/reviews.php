<?php
require_once(__DIR__ . '/../config/db.php');

// Get property ID from URL
$property_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($property_id === 0) {
    echo '<p>Error: Property not found</p>';
    exit;
}

$error = '';
$success = '';

// Handle review submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_review'])) {
    $guest_name = trim($_POST['guest_name'] ?? '');
    $guest_email = trim($_POST['guest_email'] ?? '');
    $rating = (int)($_POST['rating'] ?? 0);
    $review_text = trim($_POST['review_text'] ?? '');

    // Validation
    if (empty($guest_name) || empty($guest_email) || empty($review_text) || $rating < 1 || $rating > 5) {
        $error = 'Please fill in all fields correctly.';
    } elseif (!filter_var($guest_email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } else {
        try {
            $stmt = $pdo->prepare('
                INSERT INTO reviews (property_id, guest_name, guest_email, rating, review_text)
                VALUES (?, ?, ?, ?, ?)
            ');
            $stmt->execute([$property_id, $guest_name, $guest_email, $rating, $review_text]);
            $success = 'Thank you! Your review has been posted.';
            $_POST = []; // Clear form
        } catch (PDOException $e) {
            $error = 'Error posting review. Please try again.';
        }
    }
}

// Fetch all reviews for this property
$stmt = $pdo->prepare('
    SELECT * FROM reviews 
    WHERE property_id = ? 
    ORDER BY created_at DESC
');
$stmt->execute([$property_id]);
$reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate average rating
$stmt = $pdo->prepare('
    SELECT AVG(rating) AS avg_rating, COUNT(*) AS total_reviews 
    FROM reviews 
    WHERE property_id = ?
');
$stmt->execute([$property_id]);
$rating_info = $stmt->fetch(PDO::FETCH_ASSOC);

$avg_rating = round($rating_info['avg_rating'] ?? 0, 1);
$total_reviews = $rating_info['total_reviews'] ?? 0;
?>

<div class="reviews-section">
    <!-- Overall Rating Display -->
    <div class="reviews-header">
        <div class="rating-box">
            <div class="average-rating">
                <span class="rating-number"><?= $avg_rating ?></span>
                <span class="out-of">out of 5</span>
            </div>
            <div class="stars-display">
                <?php 
                for ($i = 1; $i <= 5; $i++) {
                    if ($i <= floor($avg_rating)) {
                        echo '<span class="star filled">★</span>';
                    } elseif ($i - $avg_rating < 1) {
                        echo '<span class="star half">★</span>';
                    } else {
                        echo '<span class="star empty">★</span>';
                    }
                }
                ?>
            </div>
            <p class="total-reviews"><?= $total_reviews ?> review<?= $total_reviews !== 1 ? 's' : '' ?></p>
        </div>
    </div>

    <!-- Write Review Form -->
    <div class="write-review-section">
        <h3>Leave a Review</h3>

        <?php if ($error): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <form method="POST" class="review-form">
            <div class="form-group">
                <label for="guest_name">Your Name *</label>
                <input 
                    type="text" 
                    id="guest_name" 
                    name="guest_name" 
                    value="<?= htmlspecialchars($_POST['guest_name'] ?? '') ?>"
                    required
                >
            </div>

            <div class="form-group">
                <label for="guest_email">Your Email *</label>
                <input 
                    type="email" 
                    id="guest_email" 
                    name="guest_email" 
                    value="<?= htmlspecialchars($_POST['guest_email'] ?? '') ?>"
                    required
                >
            </div>

            <div class="form-group">
                <label for="rating">Rating *</label>
                <div class="rating-input">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <label class="star-label">
                            <input 
                                type="radio" 
                                name="rating" 
                                value="<?= $i ?>"
                                <?= ($_POST['rating'] ?? '') == $i ? 'checked' : '' ?>
                                required
                            >
                            <span class="star-radio">★</span>
                        </label>
                    <?php endfor; ?>
                </div>
            </div>

            <div class="form-group">
                <label for="review_text">Your Review *</label>
                <textarea 
                    id="review_text" 
                    name="review_text" 
                    rows="5"
                    placeholder="Share your experience with this property..."
                    required
                ><?= htmlspecialchars($_POST['review_text'] ?? '') ?></textarea>
            </div>

            <button type="submit" name="submit_review" class="btn-submit">Post Review</button>
        </form>
    </div>

    <!-- Display Reviews -->
    <div class="reviews-list-section">
        <h3>Guest Reviews (<?= $total_reviews ?>)</h3>

        <?php if (empty($reviews)): ?>
            <p class="no-reviews">No reviews yet. Be the first to review this property!</p>
        <?php else: ?>
            <div class="reviews-list">
                <?php foreach ($reviews as $review): ?>
                    <div class="review-item">
                        <div class="review-header">
                            <div>
                                <h4 class="reviewer-name"><?= htmlspecialchars($review['guest_name']) ?></h4>
                                <p class="review-date">
                                    <?= date('F d, Y', strtotime($review['created_at'])) ?>
                                </p>
                            </div>
                            <div class="review-rating">
                                <?php 
                                for ($i = 0; $i < 5; $i++) {
                                    if ($i < $review['rating']) {
                                        echo '<span class="star filled">★</span>';
                                    } else {
                                        echo '<span class="star empty">★</span>';
                                    }
                                }
                                ?>
                                <span class="rating-text"><?= $review['rating'] ?>/5</span>
                            </div>
                        </div>
                        <p class="review-text"><?= nl2br(htmlspecialchars($review['review_text'])) ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
    .reviews-section {
        background: #f9f9f9;
        padding: 30px;
        border-radius: 10px;
        margin: 40px 0;
    }

    .reviews-header {
        margin-bottom: 40px;
    }

    .rating-box {
        background: white;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        text-align: center;
    }

    .average-rating {
        margin-bottom: 15px;
    }

    .rating-number {
        font-size: 48px;
        font-weight: bold;
        color: #667eea;
    }

    .out-of {
        font-size: 14px;
        color: #666;
    }

    .stars-display {
        font-size: 24px;
        margin: 10px 0;
        letter-spacing: 3px;
    }

    .star {
        color: #ddd;
    }

    .star.filled {
        color: #ffc107;
    }

    .star.half {
        background: linear-gradient(90deg, #ffc107 50%, #ddd 50%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .total-reviews {
        color: #666;
        margin: 10px 0 0 0;
    }

    /* Write Review Form */
    .write-review-section {
        background: white;
        padding: 25px;
        border-radius: 8px;
        margin-bottom: 40px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }

    .write-review-section h3 {
        color: #333;
        margin-bottom: 20px;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        color: #333;
        font-weight: 600;
    }

    .form-group input[type="text"],
    .form-group input[type="email"],
    .form-group textarea {
        width: 100%;
        padding: 12px;
        border: 1px solid #ddd;
        border-radius: 5px;
        font-family: 'Poppins', sans-serif;
        font-size: 14px;
    }

    .form-group input:focus,
    .form-group textarea:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 0 5px rgba(102, 126, 234, 0.3);
    }

    .form-group textarea {
        resize: vertical;
        min-height: 120px;
    }

    /* Star Rating Input */
    .rating-input {
        display: flex;
        gap: 15px;
        font-size: 32px;
    }

    .star-label {
        cursor: pointer;
        display: inline-block;
    }

    .star-label input[type="radio"] {
        display: none;
    }

    .star-radio {
        color: #ddd;
        transition: color 0.2s;
        cursor: pointer;
    }

    .star-label input[type="radio"]:checked ~ .star-radio,
    .star-label:hover .star-radio,
    .star-label:hover ~ .star-label .star-radio {
        color: #ffc107;
    }

    /* Submit Button */
    .btn-submit {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 12px 30px;
        border: none;
        border-radius: 5px;
        font-weight: 600;
        cursor: pointer;
        transition: transform 0.2s;
        font-family: 'Poppins', sans-serif;
        font-size: 14px;
    }

    .btn-submit:hover {
        transform: translateY(-2px);
    }

    /* Alerts */
    .alert {
        padding: 15px;
        border-radius: 5px;
        margin-bottom: 20px;
    }

    .alert-error {
        background: #ffebee;
        color: #d32f2f;
        border-left: 4px solid #d32f2f;
    }

    .alert-success {
        background: #e8f5e9;
        color: #388e3c;
        border-left: 4px solid #388e3c;
    }

    /* Reviews List */
    .reviews-list-section {
        background: white;
        padding: 25px;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }

    .reviews-list-section h3 {
        color: #333;
        margin-bottom: 25px;
    }

    .no-reviews {
        color: #666;
        text-align: center;
        padding: 30px;
        font-style: italic;
    }

    .reviews-list {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .review-item {
        border: 1px solid #e0e0e0;
        padding: 20px;
        border-radius: 8px;
        background: #fafafa;
    }

    .review-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 15px;
    }

    .reviewer-name {
        color: #333;
        margin: 0 0 5px 0;
        font-size: 16px;
    }

    .review-date {
        color: #999;
        font-size: 13px;
        margin: 0;
    }

    .review-rating {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 16px;
    }

    .review-rating .star {
        font-size: 18px;
        color: #ddd;
    }

    .review-rating .star.filled {
        color: #ffc107;
    }

    .rating-text {
        color: #666;
        font-weight: 600;
    }

    .review-text {
        color: #555;
        line-height: 1.6;
        margin: 0;
    }

    @media (max-width: 768px) {
        .review-header {
            flex-direction: column;
        }

        .review-rating {
            margin-top: 10px;
        }

        .rating-input {
            gap: 8px;
            font-size: 24px;
        }
    }
</style>