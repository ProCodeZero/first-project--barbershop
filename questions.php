<?php
require_once 'includes/functions.php';

include 'includes/header.php';
?>

<main class="main--questions">
    <div class="main-wrapper--questions">
        <div class="main__head">
            <h2 class="main__title">Top 10 Questions You Ask Us</h2>
            <img src="pictures/icons/message.svg" width="30px" height="30px" />
        </div>
        
        <?php 
        $faq = getFAQ();
        foreach ($faq as $index => $item): 
        ?>
        <div class="main--questions__item item">
            <h2 class="item__title">Question #<?php echo $index + 1; ?></h2>
            <div class="item__question">
                <?php echo htmlspecialchars($item['question']); ?>
                <div class="main--questions__avatar-customer">
                    <img
                        src="pictures/icons/avatar-icon.webp"
                        alt=""
                        width="50px"
                        height="50px"
                    />
                </div>
            </div>
            <div class="item__answer">
                <?php echo htmlspecialchars($item['answer']); ?>
                <div class="main--questions__avatar-assistant">
                    <img
                        src="pictures/icons/mustache.svg"
                        alt=""
                        width="40px"
                        height="40px"
                    />
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</main>

<?php include 'includes/footer.php'; ?>
