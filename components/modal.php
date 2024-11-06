<?php
function renderModal($modalId, $title, $content, $buttonText = "Close", $buttonAction = null) {
    $buttonActionAttribute = $buttonAction ? "onclick='$buttonAction'" : "onclick='closeModal(\"$modalId\")'";
    
    echo "
    <div class='modal-overlay' id='$modalId' style='display: none;'>
        <div class='modal-content'>
            <span class='close-btn' onclick='closeModal(\"$modalId\")'>&times;</span>
            <h2>$title</h2>
            <div class='modal-body'>" . htmlspecialchars_decode($content) . "</div>
        </div>
    </div>
    ";
}
?>
