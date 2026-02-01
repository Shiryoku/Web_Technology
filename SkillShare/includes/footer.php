</main>
<footer
    style="background: var(--card-bg); border-top: 1px solid var(--border-color); padding: 3rem 0; margin-top: 4rem;">
    <div class="container text-center">
        <p class="text-muted">&copy; <?php echo date('Y'); ?> SkillShare. All rights reserved.</p>
    </div>
</footer>

<!-- Chat Widget -->
<div id="chat-widget" class="chat-widget">
    <button id="chat-toggle-btn" class="chat-toggle-btn">
        <i data-lucide="message-circle"></i>
    </button>

    <div id="chat-window" class="chat-window">
        <div class="chat-header">
            <h3>AI Assistant</h3>
            <button id="chat-close-btn" class="chat-close-btn">
                <i data-lucide="x"></i>
            </button>
        </div>
        <div id="chat-messages" class="chat-messages">
            <div class="message bot">
                Hi! I can help you with registration, event creation, and booking. What would you like to know?
            </div>
        </div>
        <div class="chat-input-area">
            <input type="text" id="chat-message-input" class="chat-input" placeholder="Type your question...">
            <button id="chat-send-btn" class="chat-send-btn">
                <i data-lucide="send"></i>
            </button>
        </div>
    </div>
</div>

<?php
// Determine Base Path for Assets:
// Checks if the current script is in a subdirectory (organizer or student)
// and adjusts the path to assets (CSS/JS) accordingly.
if (!isset($base_path)) {
    $base_path = (strpos($_SERVER['SCRIPT_NAME'], '/organizer/') !== false || strpos($_SERVER['SCRIPT_NAME'], '/student/') !== false || strpos($_SERVER['SCRIPT_NAME'], '/admin/') !== false) ? '../' : '';
}
?>
<link rel="stylesheet" href="<?php echo $base_path; ?>assets/css/chat.css">
<script>
    const basePath = "<?php echo $base_path; ?>";
</script>
<script src="<?php echo $base_path; ?>assets/js/chat.js"></script>
<script>
    lucide.createIcons();
</script>
<?php
// Close database connection
if (isset($pdo)) {
    $pdo = null;
}
?>
</body>

</html>