<?php
include 'includes/auth_check.php';

// Include database connection
require_once '../includes/dbh.inc.php';

// Initialize variables
$message = '';
$section = isset($_GET['section']) ? $_GET['section'] : 'services'; // Default to services
$action = isset($_GET['action']) ? $_GET['action'] : 'list'; // list, edit, add
$item_id = isset($_GET['id']) ? $_GET['id'] : null;

// Handle Form Submissions (Add/Edit)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $section = isset($_POST['section']) ? $_POST['section'] : 'services';

    if (isset($_POST['delete'])) {
        $item_id = (int)$_POST['id'];
        deleteItem($pdo, $section, $item_id);
    } elseif (isset($_POST['save'])) {
        $item_id = isset($_POST['id']) ? $_POST['id'] : null;
        saveItem($pdo, $section, $item_id, $_POST);
    }
}

// Function to delete an item
function deleteItem($pdo, $section, $id) {
    global $message;
    try {
        switch ($section) {
            case 'services':
                $stmt = $pdo->prepare("DELETE FROM services WHERE id = ?");
                break;
            case 'products':
                $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
                break;
            case 'faq':
                $stmt = $pdo->prepare("DELETE FROM faq WHERE id = ?");
                break;
            default:
                throw new Exception("Invalid section.");
        }
        $stmt->execute(array($id));
        $message = array('type' => 'success', 'text' => ucfirst($section) . ' item deleted successfully.');
    } catch (Exception $e) {
        $message = array('type' => 'error', 'text' => 'Error deleting item: ' . $e->getMessage());
    }
}

// Function to save (insert or update) an item
function saveItem($pdo, $section, $id, $data) {
    global $message;
    try {
        switch ($section) {
            case 'services':
                $name = isset($data['name']) ? $data['name'] : '';
                $price = isset($data['price']) ? $data['price'] : 0;
                $description = isset($data['description']) ? $data['description'] : '';

                if (empty($name) || empty($price)) {
                    throw new Exception("Name and Price are required.");
                }

                if ($id) {
                    $stmt = $pdo->prepare("UPDATE services SET name = ?, price = ?, description = ? WHERE id = ?");
                    $stmt->execute(array($name, $price, $description, $id));
                } else {
                    $stmt = $pdo->prepare("INSERT INTO services (name, price, description) VALUES (?, ?, ?)");
                    $stmt->execute(array($name, $price, $description));
                }
                break;

            case 'products':
                $name = isset($data['name']) ? $data['name'] : '';
                $price = isset($data['price']) ? $data['price'] : 0;
                $image = isset($data['image']) ? $data['image'] : '';
                $description = isset($data['description']) ? $data['description'] : '';

                if (empty($name) || empty($price)) {
                    throw new Exception("Name and Price are required.");
                }

                if ($id) {
                    $stmt = $pdo->prepare("UPDATE products SET name = ?, price = ?, image = ?, description = ? WHERE id = ?");
                    $stmt->execute(array($name, $price, $image, $description, $id));
                } else {
                    $stmt = $pdo->prepare("INSERT INTO products (name, price, image, description) VALUES (?, ?, ?, ?)");
                    $stmt->execute(array($name, $price, $image, $description));
                }
                break;

            case 'faq':
                $question = isset($data['question']) ? $data['question'] : '';
                $answer = isset($data['answer']) ? $data['answer'] : '';
                $sort_order = isset($data['sort_order']) ? $data['sort_order'] : 0;

                if (empty($question) || empty($answer)) {
                    throw new Exception("Question and Answer are required.");
                }

                if ($id) {
                    $stmt = $pdo->prepare("UPDATE faq SET question = ?, answer = ?, sort_order = ? WHERE id = ?");
                    $stmt->execute(array($question, $answer, $sort_order, $id));
                } else {
                    $stmt = $pdo->prepare("INSERT INTO faq (question, answer, sort_order) VALUES (?, ?, ?)");
                    $stmt->execute(array($question, $answer, $sort_order));
                }
                break;

            default:
                throw new Exception("Invalid section.");
        }

        $message = array('type' => 'success', 'text' => ucfirst($section) . ' item saved successfully.');
        // After saving, redirect to list view to prevent form resubmission
        header("Location: pages.php?section=" . $section);
        exit();
    } catch (Exception $e) {
        $message = array('type' => 'error', 'text' => 'Error saving item: ' . $e->getMessage());
    }
}

// Function to get all items for a section
function getItems($pdo, $section) {
    try {
        switch ($section) {
            case 'services':
                $stmt = $pdo->query("SELECT id, name, price, description FROM services ORDER BY name ASC");
                break;
            case 'products':
                $stmt = $pdo->query("SELECT id, name, price, image, description FROM products ORDER BY name ASC");
                break;
            case 'faq':
                $stmt = $pdo->query("SELECT id, question, answer, sort_order FROM faq ORDER BY sort_order ASC, id ASC");
                break;
            default:
                return array();
        }
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        return array();
    }
}

// Function to get a single item by ID
function getItemById($pdo, $section, $id) {
    try {
        switch ($section) {
            case 'services':
                $stmt = $pdo->prepare("SELECT id, name, price, description FROM services WHERE id = ?");
                break;
            case 'products':
                $stmt = $pdo->prepare("SELECT id, name, price, image, description FROM products WHERE id = ?");
                break;
            case 'faq':
                $stmt = $pdo->prepare("SELECT id, question, answer, sort_order FROM faq WHERE id = ?");
                break;
            default:
                return null;
        }
        $stmt->execute(array($id));
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        return null;
    }
}

// If action is 'edit' or 'add', fetch the item (if editing)
$current_item = null;
if ($action === 'edit' && $item_id) {
    $current_item = getItemById($pdo, $section, $item_id);
} elseif ($action === 'add') {
    $current_item = array(); // Empty array for a new item
}

// If we're in 'edit' or 'add' mode, show the form.
if ($action === 'edit' || $action === 'add') {
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Manage <?= ucfirst($section) ?> - <?= ($action === 'edit') ? 'Edit' : 'Add New' ?></title>
        <link rel="stylesheet" href="../css/style.css">
    </head>
    <body>
        <div class="wrapper">
            <?php include 'header.php'; ?>
            <main style="padding: 40px; max-width: 800px; margin: 0 auto;">
                <h1><?= ($action === 'edit') ? 'Edit' : 'Add New' ?> <?= ucfirst($section) ?> Item</h1>

                <?php if (isset($message)): ?>
                    <div style="padding: 10px; margin-bottom: 20px; background: <?= $message['type'] === 'success' ? '#d4edda' : '#f8d7da' ?>; border: 1px solid <?= $message['type'] === 'success' ? '#c3e6cb' : '#f5c6cb' ?>; color: <?= $message['type'] === 'success' ? '#155724' : '#721c24' ?>;">
                        <?= htmlspecialchars($message['text']) ?>
                    </div>
                <?php endif; ?>

                <form method="POST">
                    <input type="hidden" name="section" value="<?= htmlspecialchars($section) ?>">
                    <input type="hidden" name="id" value="<?= isset($current_item['id']) ? htmlspecialchars($current_item['id']) : '' ?>">

                    <?php if ($section === 'services' || $section === 'products'): ?>
                        <div style="margin-bottom: 15px;">
                            <label for="name" style="display: block; margin-bottom: 5px; font-weight: bold;">Name:</label>
                            <input type="text" name="name" id="name" value="<?= isset($current_item['name']) ? htmlspecialchars($current_item['name']) : '' ?>" required style="width: 100%; padding: 8px; box-sizing: border-box;">
                        </div>
                        <div style="margin-bottom: 15px;">
                            <label for="price" style="display: block; margin-bottom: 5px; font-weight: bold;">Price (RUB):</label>
                            <input type="number" step="0.01" name="price" id="price" value="<?= isset($current_item['price']) ? htmlspecialchars($current_item['price']) : '' ?>" required style="width: 100%; padding: 8px; box-sizing: border-box;">
                        </div>
                        <div style="margin-bottom: 15px;">
                            <label for="description" style="display: block; margin-bottom: 5px; font-weight: bold;">Description:</label>
                            <textarea name="description" id="description" rows="4" style="width: 100%; padding: 8px; box-sizing: border-box;"><?= isset($current_item['description']) ? htmlspecialchars($current_item['description']) : '' ?></textarea>
                        </div>
                        <?php if ($section === 'products'): ?>
                            <div style="margin-bottom: 15px;">
                                <label for="image" style="display: block; margin-bottom: 5px; font-weight: bold;">Image Path (e.g., pictures/card1.png):</label>
                                <input type="text" name="image" id="image" value="<?= isset($current_item['image']) ? htmlspecialchars($current_item['image']) : '' ?>" style="width: 100%; padding: 8px; box-sizing: border-box;">
                            </div>
                        <?php endif; ?>
                    <?php elseif ($section === 'faq'): ?>
                        <div style="margin-bottom: 15px;">
                            <label for="question" style="display: block; margin-bottom: 5px; font-weight: bold;">Question:</label>
                            <textarea name="question" id="question" rows="3" required style="width: 100%; padding: 8px; box-sizing: border-box;"><?= isset($current_item['question']) ? htmlspecialchars($current_item['question']) : '' ?></textarea>
                        </div>
                        <div style="margin-bottom: 15px;">
                            <label for="answer" style="display: block; margin-bottom: 5px; font-weight: bold;">Answer:</label>
                            <textarea name="answer" id="answer" rows="5" required style="width: 100%; padding: 8px; box-sizing: border-box;"><?= isset($current_item['answer']) ? htmlspecialchars($current_item['answer']) : '' ?></textarea>
                        </div>
                        <div style="margin-bottom: 15px;">
                            <label for="sort_order" style="display: block; margin-bottom: 5px; font-weight: bold;">Sort Order:</label>
                            <input type="number" name="sort_order" id="sort_order" value="<?= isset($current_item['sort_order']) ? htmlspecialchars($current_item['sort_order']) : '0' ?>" style="width: 100%; padding: 8px; box-sizing: border-box;">
                        </div>
                    <?php endif; ?>

                    <div style="display: flex; gap: 10px;">
                        <button type="submit" name="save" style="padding: 10px 20px; background: #28a745; color: white; border: none; border-radius: 4px; cursor: pointer;">Save</button>
                        <a href="pages.php?section=<?= $section ?>" style="padding: 10px 20px; background: #6c757d; color: white; text-decoration: none; border-radius: 4px; text-align: center;">Cancel</a>
                    </div>
                </form>
            </main>
            <?php include 'footer.php'; ?>
        </div>
    </body>
    </html>
    <?php
    exit(); // Stop further execution after showing the form
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Pages Content</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="wrapper">
        <?php include 'header.php'; ?>
        <main style="padding: 40px;">
            <h1>Manage Site Content</h1>

            <?php if (isset($message)): ?>
                <div style="padding: 10px; margin-bottom: 20px; background: <?= $message['type'] === 'success' ? '#d4edda' : '#f8d7da' ?>; border: 1px solid <?= $message['type'] === 'success' ? '#c3e6cb' : '#f5c6cb' ?>; color: <?= $message['type'] === 'success' ? '#155724' : '#721c24' ?>;">
                    <?= htmlspecialchars($message['text']) ?>
                </div>
            <?php endif; ?>

            <!-- Section Tabs -->
            <div style="margin-bottom: 30px;">
                <a href="?section=services" style="padding: 10px 20px; text-decoration: none; color: #495057; <?= $section === 'services' ? 'border-bottom: 3px solid #007bff; font-weight: bold;' : '' ?>">Services</a>
                <a href="?section=products" style="padding: 10px 20px; text-decoration: none; color: #495057; <?= $section === 'products' ? 'border-bottom: 3px solid #007bff; font-weight: bold;' : '' ?>">Products</a>
                <a href="?section=faq" style="padding: 10px 20px; text-decoration: none; color: #495057; <?= $section === 'faq' ? 'border-bottom: 3px solid #007bff; font-weight: bold;' : '' ?>">FAQ</a>
            </div>

            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h2><?= ucfirst($section) ?></h2>
                <a href="?section=<?= $section ?>&action=add" style="padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 4px;">+ Add New</a>
            </div>

            <?php
            $items = getItems($pdo, $section);
            if (empty($items)) {
                echo "<p>No items found in this section.</p>";
            } else {
                ?>
                <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
                    <thead>
                        <tr style="background: #343a40; color: #fff;">
                            <?php if ($section === 'services'): ?>
                                <th style="padding: 12px; text-align: left;">ID</th>
                                <th style="padding: 12px; text-align: left;">Name</th>
                                <th style="padding: 12px; text-align: left;">Price</th>
                                <th style="padding: 12px; text-align: left;">Description</th>
                                <th style="padding: 12px; text-align: left;">Actions</th>
                            <?php elseif ($section === 'products'): ?>
                                <th style="padding: 12px; text-align: left;">ID</th>
                                <th style="padding: 12px; text-align: left;">Name</th>
                                <th style="padding: 12px; text-align: left;">Price</th>
                                <th style="padding: 12px; text-align: left;">Image</th>
                                <th style="padding: 12px; text-align: left;">Description</th>
                                <th style="padding: 12px; text-align: left;">Actions</th>
                            <?php elseif ($section === 'faq'): ?>
                                <th style="padding: 12px; text-align: left;">ID</th>
                                <th style="padding: 12px; text-align: left;">Question</th>
                                <th style="padding: 12px; text-align: left;">Answer</th>
                                <th style="padding: 12px; text-align: left;">Sort Order</th>
                                <th style="padding: 12px; text-align: left;">Actions</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $item): ?>
                        <tr style="border-bottom: 1px solid #dee2e6;">
                            <td style="padding: 12px;"><?= $item['id'] ?></td>
                            <?php if ($section === 'services'): ?>
                                <td style="padding: 12px;"><?= htmlspecialchars($item['name']) ?></td>
                                <td style="padding: 12px;"><?= number_format($item['price'], 2) ?> ₽</td>
                                <td style="padding: 12px;"><?= htmlspecialchars(substr($item['description'], 0, 50)) . (strlen($item['description']) > 50 ? '...' : '') ?></td>
                            <?php elseif ($section === 'products'): ?>
                                <td style="padding: 12px;"><?= htmlspecialchars($item['name']) ?></td>
                                <td style="padding: 12px;"><?= number_format($item['price'], 2) ?> ₽</td>
                                <td style="padding: 12px;"><img src="../<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>" style="max-height: 50px;"></td>
                                <td style="padding: 12px;"><?= htmlspecialchars(substr($item['description'], 0, 50)) . (strlen($item['description']) > 50 ? '...' : '') ?></td>
                            <?php elseif ($section === 'faq'): ?>
                                <td style="padding: 12px;"><?= htmlspecialchars(substr($item['question'], 0, 50)) . (strlen($item['question']) > 50 ? '...' : '') ?></td>
                                <td style="padding: 12px;"><?= htmlspecialchars(substr($item['answer'], 0, 50)) . (strlen($item['answer']) > 50 ? '...' : '') ?></td>
                                <td style="padding: 12px;"><?= $item['sort_order'] ?></td>
                            <?php endif; ?>
                            <td style="padding: 12px;">
                                <a href="?section=<?= $section ?>&action=edit&id=<?= $item['id'] ?>" style="padding: 5px 10px; background: #ffc107; color: #212529; text-decoration: none; border-radius: 4px; margin-right: 5px;">Edit</a>
                                <form method="POST" onsubmit="return confirm('Are you sure you want to delete this item?');" style="display: inline;">
                                    <input type="hidden" name="section" value="<?= $section ?>">
                                    <input type="hidden" name="id" value="<?= $item['id'] ?>">
                                    <button type="submit" name="delete" style="padding: 5px 10px; background: #dc3545; color: white; border: none; border-radius: 4px; cursor: pointer;">Delete</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php
            }
            ?>
        </main>
        <?php include 'footer.php'; ?>
    </div>
</body>
</html>